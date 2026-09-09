<?php

namespace App\Services\Handlers;

use App\Enums\AmountFlow;
use App\Models\Transaction;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\TransactionNotifierService;
use Wallet;

class VoucherHandler implements SuccessHandlerInterface
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

            $this->notifier->toUser($transaction, 'voucher_user_redeemed', [
                'amount'       => $transaction->amount.' '.$transaction->currency,
                'voucher_code' => $transaction->voucher_code ?? 'N/A',
                'trx'          => $transaction->trx_id,
            ]);
        }
    }
}
