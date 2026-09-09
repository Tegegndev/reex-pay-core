<?php

namespace App\Services\Handlers;

use App\Enums\AmountFlow;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\TransactionNotifierService;
use Wallet;

class SendMoneyHandler implements SuccessHandlerInterface
{
    protected TransactionNotifierService $notifier;

    public function __construct(TransactionNotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handleSuccess(Transaction $transaction): void
    {
        // Deduct from sender or add to receiver
        if ($transaction->amount_flow === AmountFlow::MINUS) {
            Wallet::subtractMoneyByWalletUuid($transaction->wallet_reference, $transaction->payable_amount);

            $recipientName = User::find($transaction->ref_user_id)->name ?? __('Recipient');

            $this->notifier->toUser($transaction, 'send_money_user_sent', [
                'amount'    => $transaction->amount.' '.$transaction->currency,
                'recipient' => $recipientName,
                'trx'       => $transaction->trx_id,
            ]);

        } elseif ($transaction->amount_flow === AmountFlow::PLUS) {
            Wallet::addMoneyByWalletUuid($transaction->wallet_reference, $transaction->net_amount);

            $senderName = User::find($transaction->ref_user_id)->name ?? __('Sender');

            $this->notifier->toUser($transaction,
                identifier: 'send_money_user_received',
                data: [
                    'amount' => $transaction->amount.' '.$transaction->currency,
                    'sender' => $senderName,
                    'trx'    => $transaction->trx_id,
                ]
            );
        }
    }
}
