<?php

namespace App\Listeners;

use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Events\TransactionUpdated;
use App\Models\User;
use App\Models\UserRank;
use App\Notifications\TemplateNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Transaction;
use Wallet;

class UpdateUserRanking
{
    /**
     * Handle the event.
     */
    public function handle(TransactionUpdated $event): void
    {
        $user    = $event->user;
        $lockKey = "user_ranking_update_{$user->id}";

        // Prevent concurrent updates using a cache lock
        $lock = Cache::lock($lockKey, 5);

        if ($lock->get()) {
            try {
                $eligibleRank = $this->getHighestEligibleRank($user);

                if ($eligibleRank && $this->shouldPromote($user, $eligibleRank)) {
                    $this->promoteUserRank($user, $eligibleRank);
                }
            } finally {
                $lock->release();
            }
        }
    }

    /**
     * Determine the highest eligible rank based on transaction sums.
     */
    protected function getHighestEligibleRank(User $user): ?UserRank
    {
        return UserRank::active()
            ->orderByDesc('transaction_amount')
            ->get()
            ->filter(function ($rank) use ($user) {
                if (empty($rank->transaction_types)) {
                    return false;
                }

                $sum = $user->transactions()
                    ->whereIn('trx_type', $rank->transaction_types)
                    ->sum('amount');

                return $sum >= $rank->transaction_amount;
            })
            ->first(); // Return the top-most eligible rank
    }

    /**
     * Determine if the user should be promoted.
     */
    protected function shouldPromote(User $user, UserRank $rank): bool
    {
        return ! $user->rank || $rank->transaction_amount > $user->rank->transaction_amount;
    }

    /**
     * Promote the user to the specified rank and apply rewards.
     */
    protected function promoteUserRank(User $user, UserRank $newRank): void
    {
        DB::transaction(function () use ($user, $newRank) {
            // Get all lower rank IDs
            $lowerRankIds = UserRank::active()
                ->where('transaction_amount', '<', $newRank->transaction_amount)
                ->pluck('id')
                ->toArray();

            $oldRanks = array_unique(array_merge($user->old_ranks ?? [], $lowerRankIds));

            $user->update([
                'rank_id'   => $newRank->id,
                'old_ranks' => $oldRanks,
            ]);

            if ($newRank->reward > 0) {
                $wallet = $user->default_Wallet;

                Wallet::addMoney($wallet, $newRank->reward);
                $this->createTransaction($user, $wallet, $newRank->reward);
            }
        });
    }

    /**
     * Log the reward transaction and notify the user.
     *
     * @param \App\Models\Wallet $wallet
     */
    protected function createTransaction(User $user, $wallet, float $amount): void
    {
        $transactionData = new TransactionData(
            user_id: $user->id,
            trx_type: TrxType::REWARD,
            amount: $amount,
            amount_flow: AmountFlow::PLUS,
            currency: $wallet->currency->code,
            net_amount: $amount,
            payable_amount: $amount,
            payable_currency: $wallet->currency->code,
            wallet_reference: $wallet->uuid,
            description: __('Reward for reaching a new rank'),
            status: TrxStatus::COMPLETED
        );

        Transaction::create($transactionData);

        $user->notify(new TemplateNotification(
            identifier: 'reward_user_system',
            data: [
                'amount'        => $amount.' '.$wallet->currency->code,
                'reward_reason' => __('You have reached a new rank and received a reward'),
            ],
            action: route('user.transaction.index')
        ));
    }
}
