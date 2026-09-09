<?php

namespace App\Services\Handlers;

use App\Enums\AmountFlow;
use App\Models\Transaction;
use App\Services\Handlers\Interfaces\FailHandlerInterface;
use App\Services\Handlers\Interfaces\SubmittedHandlerInterface;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\TransactionNotifierService;
use Wallet;

class RequestMoneyHandler implements FailHandlerInterface, SubmittedHandlerInterface, SuccessHandlerInterface
{
    protected TransactionNotifierService $notifier;

    public function __construct(TransactionNotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handleSuccess(Transaction $transaction): void
    {
        if ($transaction->amount_flow === AmountFlow::PLUS) {
            Wallet::addMoneyByWalletUuid($transaction->wallet_reference, $transaction->net_amount);
            $receiver = Transaction::where('trx_id', $transaction->trx_reference)->first()?->user->name;
            $this->notifier->toUser($transaction, 'request_money_user_approved', [
                'amount'   => $transaction->amount.' '.$transaction->currency,
                'receiver' => $receiver,
                'trx'      => $transaction->trx_id,
            ]);
        } elseif ($transaction->amount_flow === AmountFlow::MINUS) {
            Wallet::subtractMoneyByWalletUuid($transaction->wallet_reference, $transaction->payable_amount);
        }
    }

    public function handleFail(Transaction $transaction): void
    {
        $receiver = Transaction::where('trx_id', $transaction->trx_reference)->first()?->user->name;

        $this->notifier->toUser($transaction, 'request_money_user_rejected', [
            'amount'   => $transaction->amount.' '.$transaction->currency,
            'receiver' => $receiver,
            'trx'      => $transaction->trx_id,
        ]);
    }

    public function handleSubmitted(Transaction $transaction): void
    {
        $senderName   = $transaction->user->name;
        $recipientTrx = Transaction::where('trx_id', $transaction->trx_reference)->first();

        $this->notifier->toUser($transaction, 'request_money_user_requested',
            [
                'amount'    => $transaction->amount.' '.$transaction->currency,
                'recipient' => $recipientTrx?->user?->name ?? 'Recipient',
                'trx'       => $transaction->trx_id,
            ],
            route('user.transaction.index')
        );

        if ($recipientTrx) {
            $this->notifier->toUser($recipientTrx, 'request_money_user_received',
                [
                    'amount' => $transaction->amount.' '.$transaction->currency,
                    'sender' => $senderName,
                    'trx'    => $transaction->trx_id,
                ],
                route('user.transaction.index')
            );
        }
    }
}
