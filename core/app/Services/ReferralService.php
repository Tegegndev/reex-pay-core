<?php

namespace App\Services;

use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Models\Referral;
use App\Models\Reward;
use App\Models\Transaction;
use App\Notifications\TemplateNotification;
use Transaction as TransactionServiceFacade;
use Wallet;

class ReferralService
{
    public function rewardReferral($type, $baseAmount)
    {
        $user     = auth()->user();
        $referral = Referral::where('referred_user_id', $user->id)->first();
        $maxLevel = $user->rank->features['referral_level'];

        if ($referral) {
            $this->distributeRewards($referral, $type, $baseAmount, 1, $maxLevel);
        }
    }

    protected function distributeRewards($referral, $type, $amount, $level, $maxLevel)
    {
        if (! $referral || $level > $maxLevel) {
            return;
        }
        $rewardPercentage = Reward::where('type', $type)
            ->where('level', $level)
            ->value('percentage');

        if ($rewardPercentage > 0) {
            $referralUser              = $referral->user;
            $referralUserDefaultWallet = $referralUser->defaultWallet;
            $rewardAmount              = ($amount * $rewardPercentage) / 100;

            $transactionData = new TransactionData(
                user_id: $referralUser->id,
                trx_type: TrxType::REFERRAL_REWARD,
                amount: $rewardAmount,
                amount_flow: AmountFlow::PLUS,
                net_amount: $rewardAmount,
                payable_amount: $rewardAmount,
                payable_currency: siteCurrency(),
                wallet_reference: $referralUserDefaultWallet->uuid,
                description: "Level {$level} {$type} reward from ".auth()->user()->name,
                status: TrxStatus::COMPLETED
            );

            TransactionServiceFacade::create($transactionData);
            Wallet::addMoney($referralUserDefaultWallet, $rewardAmount);
            $referralUser->notify(new TemplateNotification(
                identifier: 'reward_user_referral',
                data: [
                    'amount'        => $rewardAmount.' '.siteCurrency(),
                    'referred_user' => auth()->user()->name,
                ],
                action: route('user.transaction.index')
            ));

            $this->distributeRewards($referral->parentReferral, $type, $amount, $level + 1, $maxLevel);
        }
    }

    public function shouldApplyReferral(Transaction $transaction): bool
    {
        $referredBy = $transaction->user->referredBy;
        if (! $referredBy) {
            return false;
        }

        $rewardTypes = Reward::pluck('type')->unique()->toArray();
        if (! in_array($transaction->trx_type->value, $rewardTypes)) {
            return false;
        }

        $hasSameTypeTransaction = $transaction->user->transactions()
            ->where('trx_type', $transaction->trx_type->value)
            ->where('id', '!=', $transaction->id)
            ->exists();

        return ! $hasSameTypeTransaction;
    }
}
