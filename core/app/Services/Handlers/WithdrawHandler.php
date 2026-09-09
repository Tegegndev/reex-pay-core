<?php

namespace App\Services\Handlers;

use App\Enums\MethodType;
use App\Models\Admin;
use App\Models\Transaction;
use App\Notifications\TemplateNotification;
use App\Services\Handlers\Interfaces\FailHandlerInterface;
use App\Services\Handlers\Interfaces\SubmittedHandlerInterface;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\TransactionNotifierService;
use Notification;
use Wallet;

class WithdrawHandler implements FailHandlerInterface, SubmittedHandlerInterface, SuccessHandlerInterface
{
    protected TransactionNotifierService $notifier;

    public function __construct(TransactionNotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handleSuccess(Transaction $transaction): void
    {
        $this->notifier->toUser($transaction, 'withdraw_user_approved', [
            'amount' => $transaction->amount.' '.$transaction->currency,
            'method' => $transaction->provider,
        ]);

        if ($transaction->processing_type === MethodType::AUTOMATIC) {
            $admins = Admin::permission('withdraw-notification')->get();

            Notification::send($admins, new TemplateNotification(
                identifier: 'withdraw_admin_auto_processed',
                data: [
                    'user'   => $transaction->user->name,
                    'amount' => $transaction->amount.' '.$transaction->currency,
                    'method' => $transaction->provider,
                    'trx'    => $transaction->trx_id,
                ],
                sender: $transaction->user
            ));
        }
    }

    public function handleFail(Transaction $transaction): void
    {
        $transactionData = $transaction->trx_data ?? [];

        if (! ($transactionData['wallet_refunded_on_failure'] ?? false)) {
            Wallet::addMoneyByWalletUuid($transaction->wallet_reference, $transaction->payable_amount);
            $transactionData['wallet_refunded_on_failure'] = true;
            $transaction->forceFill(['trx_data' => $transactionData])->save();
        }

        $this->notifier->toUser($transaction, 'withdraw_user_rejected', [
            'amount' => $transaction->amount.' '.$transaction->currency,
            'method' => $transaction->provider,
            'reason' => $transaction->remarks,
        ]);
    }

    public function handleSubmitted(Transaction $transaction): void
    {
        // Notify user
        $this->notifier->toUser($transaction, 'withdraw_user_requested', [
            'amount' => $transaction->amount.' '.$transaction->currency,
            'method' => $transaction->provider,
            'trx'    => $transaction->trx_id,
        ]);

        // Notify admin
        $this->notifier->toAdmins('withdraw-notification', 'withdraw_admin_manual_submitted', [
            'user'   => $transaction->user->name,
            'amount' => $transaction->amount.' '.$transaction->currency,
            'method' => $transaction->provider,
            'trx'    => $transaction->trx_id,
        ],
            sender: $transaction->user,
            action: route('admin.withdraw.manual-request')
        );

    }
}
