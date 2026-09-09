<?php

namespace App\Services\Handlers;

use App\Enums\AmountFlow;
use App\Models\Transaction;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\TransactionNotifierService;
use Wallet;

class ExchangeMoneyHandler implements SuccessHandlerInterface
{
    protected TransactionNotifierService $notifier;

    public function __construct(TransactionNotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handleSuccess(Transaction $transaction): void
    {
        if ($transaction->amount_flow === AmountFlow::MINUS) {
            Wallet::subtractMoneyByWalletUuid($transaction->wallet_reference, $transaction->payable_amount);
        }

        if ($transaction->amount_flow === AmountFlow::PLUS) {
            Wallet::addMoneyByWalletUuid($transaction->wallet_reference, $transaction->net_amount);

            $this->notifier->toUser($transaction, 'exchange_money_user_exchanged', [
                'from_amount'   => $transaction->from_amount,
                'from_currency' => $transaction->from_currency,
                'to_amount'     => $transaction->net_amount,
                'to_currency'   => $transaction->payable_currency,
                'trx'           => $transaction->trx_id,
            ]);
        }
    }
}
