<?php

namespace App\Services\Handlers;

use App\Enums\AmountFlow;
use App\Models\Transaction;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\TransactionNotifierService;
use Illuminate\Support\Facades\Log;
use Wallet;

class PaymentHandler implements SuccessHandlerInterface
{
    protected TransactionNotifierService $notifier;

    public function __construct(TransactionNotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handleSuccess(Transaction $transaction): void
    {
        // Detect if this is a sandbox/test transaction
        $isSandbox = $this->isSandboxTransaction($transaction);

        if ($transaction->amount_flow === AmountFlow::MINUS) {
            // Skip wallet operations for sandbox transactions
            if (!$isSandbox) {
                Wallet::subtractMoneyByWalletUuid($transaction->wallet_reference, $transaction->payable_amount);
            } else {
                // Log sandbox wallet operation skip
                Log::info('Sandbox transaction: Skipped wallet subtraction', [
                    'transaction_id' => $transaction->trx_id,
                    'amount' => $transaction->payable_amount,
                    'wallet_reference' => $transaction->wallet_reference,
                ]);
            }

            $merchantName = $transaction->trx_data['merchant_name'] ?? 'Merchant';
            
            // Add sandbox prefix to notification data for test transactions
            $notificationData = [
                'amount'   => $transaction->amount.' '.$transaction->currency,
                'merchant' => $isSandbox ? __('SANDBOX: :merchant', ['merchant' => $merchantName]) : $merchantName,
                'trx'      => $transaction->trx_id,
            ];

            $this->notifier->toUser($transaction, 'payment_user_made', $notificationData);
        }

        if ($transaction->amount_flow === AmountFlow::PLUS) {
            // Skip wallet operations for sandbox transactions
            if (!$isSandbox) {
                Wallet::addMoneyByWalletUuid($transaction->wallet_reference, $transaction->net_amount);
            } else {
                // Log sandbox wallet operation skip
                Log::info('Sandbox transaction: Skipped wallet addition', [
                    'transaction_id' => $transaction->trx_id,
                    'amount' => $transaction->net_amount,
                    'wallet_reference' => $transaction->wallet_reference,
                ]);
            }

            $payerTrx  = Transaction::find($transaction->trx_reference);
            $payerName = $payerTrx?->user?->name ?? 'Payer';

            // Add sandbox prefix to notification data for test transactions
            $notificationData = [
                'amount' => $transaction->amount.' '.$transaction->currency,
                'payer'  => $isSandbox ? __('SANDBOX: :payer', ['payer' => $payerName]) : $payerName,
                'trx'    => $transaction->trx_id,
            ];

            $transaction->user->notify(new \App\Notifications\TemplateNotification(
                identifier: 'payment_user_received',
                data: $notificationData
            ));
        }

        // Log successful payment handling with environment context
        Log::info('Payment handled successfully', [
            'transaction_id' => $transaction->trx_id,
            'amount_flow' => $transaction->amount_flow->value,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'is_sandbox' => $isSandbox,
            'wallet_operations_skipped' => $isSandbox,
        ]);
    }

    /**
     * Detect if transaction is a sandbox/test transaction.
     */
    private function isSandboxTransaction(Transaction $transaction): bool
    {
        // Check transaction remarks for sandbox indicator
        if (str_contains($transaction->remarks ?? '', 'SANDBOX_TRANSACTION')) {
            return true;
        }

        // Check transaction data for sandbox indicators
        if (isset($transaction->trx_data['is_sandbox']) && $transaction->trx_data['is_sandbox']) {
            return true;
        }

        if (isset($transaction->trx_data['environment']) && $transaction->trx_data['environment'] === 'sandbox') {
            return true;
        }

        return false;
    }
}
