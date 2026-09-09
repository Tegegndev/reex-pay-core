<?php

namespace App\Services\Handlers;

use App\Enums\MethodType;
use App\Models\Transaction;
use App\Services\Handlers\Interfaces\FailHandlerInterface;
use App\Services\Handlers\Interfaces\SubmittedHandlerInterface;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\ReferralService;
use App\Services\TransactionNotifierService;
use Wallet;

class DepositHandler implements FailHandlerInterface, SubmittedHandlerInterface, SuccessHandlerInterface
{
    protected TransactionNotifierService $notifier;

    public function __construct(TransactionNotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handleSuccess(Transaction $transaction): void
    {
        Wallet::addMoneyByWalletUuid($transaction->wallet_reference, $transaction->net_amount);

        if (app(ReferralService::class)->shouldApplyReferral($transaction)) {
            app(ReferralService::class)->rewardReferral($transaction->trx_type->value, $transaction->amount);
        }

        $data = [
            'amount' => $transaction->amount.' '.$transaction->currency,
            'method' => $transaction->provider,
            'trx'    => $transaction->trx_id,
        ];

        if ($transaction->processing_type === MethodType::AUTOMATIC) {
            $this->notifier->toUser(
                $transaction,
                'deposit_user_auto_success',
                $data,
                route('user.transaction.index'));

            $this->notifier->toAdmins(
                'deposit-notification',
                'deposit_admin_auto_processed',
                ['user' => $transaction->user->name, ...$data],
                $transaction->user,
                route('admin.transaction')
            );
        } else {
            $this->notifier->toUser($transaction, 'deposit_user_approved', [
                'amount' => $data['amount'],
                'method' => $data['method'],
            ]);
        }
    }

    public function handleFail(Transaction $transaction): void
    {

        $this->notifier->toUser($transaction, 'deposit_user_rejected', [
            'amount' => $transaction->amount.' '.$transaction->currency,
            'method' => $transaction->provider,
            'reason' => $transaction->remarks ?? __('N/A'),
        ]);
    }

    public function handleSubmitted(Transaction $transaction): void
    {
        $data = [
            'amount' => $transaction->amount.' '.$transaction->currency,
            'method' => $transaction->provider,
            'trx'    => $transaction->trx_id,
        ];

        $this->notifier->toUser($transaction, 'deposit_user_submitted', $data);

        $this->notifier->toAdmins(
            'deposit-notification',
            'deposit_admin_notify_submission',
            ['user' => $transaction->user->name, ...$data],
            $transaction->user,
            route('admin.deposit.manual-request')
        );
    }
}
