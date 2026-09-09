<?php

namespace App\Services;

use App\Data\TransactionData;
use App\Enums\MethodType;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Events\TransactionUpdated;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Services\Handlers\DepositHandler;
use App\Services\Handlers\Interfaces\FailHandlerInterface;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\Handlers\PaymentHandler;
use App\Services\Handlers\RequestMoneyHandler;
use App\Services\Handlers\WithdrawHandler;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wallet;

class TransactionService
{
    /*
    |--------------------------------------------------------------------------
    | Public Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Create a new transaction record.
     */
    public function create(TransactionData $data): Transaction
    {
        return Transaction::create($this->prepareTransactionData($data));
    }

    /**
     * Fetch transactions based on filters.
     */
    public function getTransactions(
        ?int $user_id = null,
        TrxType|string|null $trx_type = null,
        ?string $provider = null,
        TrxStatus|string|null $status = null,
        int $per_page = 10,
        string $sort_by = 'created_at',
        string $order = 'desc',
        ?string $search = null,
        ?string $dateRange = null,
        ?MethodType $processing_type = null
    ): LengthAwarePaginator {
        $filters = compact('user_id', 'trx_type', 'provider', 'status', 'search', 'dateRange', 'processing_type');

        return Transaction::with('user')
            ->applyFilters($filters)
            ->orderBy($sort_by, $order)
            ->paginate($per_page)
            ->withQueryString();
    }

    /**
     * Calculate current and previous stats for a specific transaction type.
     */
    public function calculateTransactionTypeStatistics(TrxType $trxType, $trxStatuses, ?int $userId = null, int $days = 7): array
    {
        $trxStatuses  = (array) $trxStatuses;
        $statusValues = array_map(fn ($s) => $s instanceof TrxStatus ? $s->value : $s, $trxStatuses);

        $range         = now();
        $currentRange  = [$range->copy()->subDays($days), $range];
        $previousRange = [$range->copy()->subDays($days * 2), $range->copy()->subDays($days)];

        $baseQuery = Transaction::where('trx_type', $trxType->value);
        if ($userId) {
            $baseQuery->where('user_id', $userId);
        }

        $current  = (clone $baseQuery)->whereBetween('created_at', $currentRange)->whereIn('status', $statusValues)->sum('amount');
        $previous = (clone $baseQuery)->whereBetween('created_at', $previousRange)->whereIn('status', $statusValues)->sum('amount');

        return [
            'current_value'   => $current,
            'previous_value'  => $previous,
            'current_percent' => $previous === 0 ? 0 : ($current / $previous) * 100,
        ];
    }

    /**
     * Complete a transaction and trigger success handler.
     */
    public function completeTransaction(string $trxId, ?string $remarks = null, ?string $description = null): void
    {
        DB::transaction(function () use ($trxId, $remarks, $description) {
            $transaction = Transaction::where('trx_id', $trxId)->lockForUpdate()->first();
            if (! $transaction) {
                throw new \Exception("Transaction not found for ID: {$trxId}");
            }

            if ($transaction->status !== TrxStatus::PENDING) {
                return;
            }

            $this->updateTransactionStatusWithRemarks($transaction, TrxStatus::COMPLETED, $remarks, $description);

            if (($handler = $this->resolveHandler($transaction)) instanceof SuccessHandlerInterface) {
                $handler->handleSuccess($transaction);
            }

            $this->sendMerchantPaymentIPN($transaction, __('Payment Completed'), TrxStatus::COMPLETED);
            event(new TransactionUpdated($transaction->user));
        });
    }

    /**
     * Fail a transaction and trigger failure handler.
     */
    public function failTransaction(string $trxId, ?string $remarks = null, ?string $description = null): void
    {
        DB::transaction(function () use ($trxId, $remarks, $description) {
            $transaction = Transaction::where('trx_id', $trxId)->lockForUpdate()->first();
            if (! $transaction) {
                throw new \Exception("Transaction not found for ID: {$trxId}");
            }

            if ($transaction->status !== TrxStatus::PENDING) {
                return;
            }

            $this->updateTransactionStatusWithRemarks($transaction, TrxStatus::FAILED, $remarks, $description);
            $this->sendMerchantPaymentIPN($transaction, 'Payment Failed', TrxStatus::FAILED);

            if (($handler = $this->resolveHandler($transaction)) instanceof FailHandlerInterface) {
                $handler->handleFail($transaction);
            }
        });
    }

    /**
     * Cancel a transaction, optionally refund amount.
     */
    public function cancelTransaction(string $trxId, ?string $remarks = null, bool $refund = false): void
    {
        $transaction = $this->findTransaction($trxId);
        if (! $transaction) {
            throw new \Exception("Transaction not found for ID: {$trxId}");
        }

        if ($refund) {
            Wallet::addMoneyByWalletUuid($transaction->wallet_reference, $transaction->payable_amount);
        }

        $this->updateTransactionStatusWithRemarks($transaction, TrxStatus::CANCELED, $remarks);
        $transaction->status = TrxStatus::CANCELED;
        if (($handler = $this->resolveHandler($transaction)) instanceof FailHandlerInterface) {
            $handler->handleFail($transaction);
        }
    }

    /**
     * Retrieve statistics for different transaction groups.
     */
    public function getTransactionStatistics(?int $userId = null): \Illuminate\Support\Collection
    {
        $trxGroups = [
            'deposit'        => [TrxType::DEPOSIT],
            'send_money'     => [TrxType::SEND_MONEY],
            'request_money'  => [TrxType::REQUEST_MONEY],
            'exchange_money' => [TrxType::EXCHANGE_MONEY],
            'payment'        => [TrxType::PAYMENT],
            'withdraw'       => [TrxType::WITHDRAW],
            'voucher'        => [TrxType::VOUCHER],
            'rewards'        => [TrxType::REWARD, TrxType::REFERRAL_REWARD],
        ];

        $transactions = Transaction::select('trx_type', 'amount', 'currency')
            ->where('status', TrxStatus::COMPLETED)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->get();

        $defaultCurrency = siteCurrency();
        $converter       = app(CurrencyConversionService::class);

        $converted = $transactions->map(function ($trx) use ($converter, $defaultCurrency) {
            $amount = $trx->currency === $defaultCurrency
                ? $trx->amount
                : $converter->convertCurrency($trx->amount, $trx->currency, $defaultCurrency);

            return [
                'trx_type' => $trx->trx_type->value,
                'amount'   => round($amount ?? 0, 2),
            ];
        });

        return collect($trxGroups)->mapWithKeys(function ($types, $key) use ($converted) {
            $sum  = $converted->whereIn('trx_type', array_map(fn ($t) => $t->value, $types))->sum('amount');
            $type = $types[0];

            return [$key => [
                'title'       => $type->label(),
                'value'       => formatCurrency($sum),
                'icon'        => $type->icon(),
                'color_class' => $type->kebabCase(),
                'link'        => $key == 'rewards' || $key == 'voucher' ? null : route('admin.transaction', ['type' => $key]),
            ]];
        });
    }

    /**
     * Find transaction by trx ID.
     */
    public function findTransaction(string $trxId): ?Transaction
    {
        return Transaction::where('trx_id', $trxId)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Protected Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Prepare transaction data array from DTO.
     */
    protected function prepareTransactionData(TransactionData $data): array
    {
        return [
            'user_id'          => $data->user_id,
            'trx_type'         => $data->trx_type->value,
            'amount'           => $data->amount,
            'amount_flow'      => $data->amount_flow?->value,
            'fee'              => $data->fee,
            'currency'         => $data->currency ?? siteCurrency(),
            'provider'         => $data->provider,
            'processing_type'  => $data->processing_type->value,
            'net_amount'       => $data->net_amount,
            'payable_amount'   => $data->payable_amount,
            'payable_currency' => $data->payable_currency,
            'wallet_reference' => $data->wallet_reference,
            'trx_data'         => $data->trx_data,
            'remarks'          => $data->remarks,
            'description'      => ucfirst($data->description),
            'trx_reference'    => $data->trx_reference,
            'status'           => $data->status->value,
            'trx_token'        => $data->trx_token,
            'expires_at'       => $data->expires_at,
        ];
    }

    /**
     * Update transaction status and metadata.
     */
    protected function updateTransactionStatusWithRemarks(Transaction $transaction, TrxStatus $status, ?string $remarks = null, ?string $description = null): void
    {
        $transaction->update(array_filter([
            'status'      => $status,
            'remarks'     => $remarks,
            'description' => $description,
        ]));

        // 🟢 Sync local object with updated values
        $transaction->status      = $status;
        $transaction->remarks     = $remarks;
        $transaction->description = $description;
    }

    /**
     * Dispatch merchant IPN notification.
     */
    protected function sendMerchantPaymentIPN(Transaction $transaction, string $message, TrxStatus $status): void
    {
        if ($transaction->trx_type !== TrxType::RECEIVE_PAYMENT) {
            return;
        }

        try {
            $trxData      = $transaction->trx_data;
            $merchant     = Merchant::findOrFail($trxData['merchant_id']);
            $clientSecret = $merchant->api_secret;

            unset($trxData['merchant_id']);

            $payload = [
                'data'      => $trxData,
                'message'   => $message,
                'status'    => $status->value,
                'timestamp' => now()->timestamp,
            ];

            $signature = hash_hmac('sha256', json_encode($payload), $clientSecret);

            $response = Http::withHeaders([
                'X-Signature'  => $signature,
                'Content-Type' => 'application/json',
            ])->post($trxData['ipn_url'], $payload);

            if (! $response->successful()) {
                Log::error('IPN failed. Status Code: '.$response->status());
            }
        } catch (\Exception $e) {
            Log::error('IPN error: '.$e->getMessage());
        }
    }

    /**
     * Resolve transaction handler based on type.
     */
    protected function resolveHandler(Transaction $transaction): mixed
    {
        return match ($transaction->trx_type) {
            TrxType::DEPOSIT         => app(DepositHandler::class),
            TrxType::RECEIVE_PAYMENT => app(PaymentHandler::class),
            TrxType::REQUEST_MONEY   => app(RequestMoneyHandler::class),
            TrxType::WITHDRAW        => app(WithdrawHandler::class),
            default                  => null,
        };
    }
}
