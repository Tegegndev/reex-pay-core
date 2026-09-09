<?php

declare(strict_types=1);

namespace App\VirtualCard;

use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\MethodType;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Enums\VirtualCard\VirtualCardRequestStatus;
use App\Exceptions\NotifyErrorException;
use App\Models\VirtualCard;
use App\Models\VirtualCardFeeSetting;
use App\Models\VirtualCardProvider;
use App\Models\VirtualCardRequest;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\Auth;
use Transaction;

class VirtualCardManager
{
    protected VirtualCardProviderFactory $providerFactory;

    public function __construct(VirtualCardProviderFactory $providerFactory)
    {
        $this->providerFactory = $providerFactory;
    }

    /**
     * Issue a virtual card using the selected provider.
     *
     * @throws Exception
     */
    public function issueProviderCard(VirtualCardRequest $request, string $providerCode): VirtualCard
    {
        $provider = $this->providerFactory->getProvider($providerCode);
        $cardInfo = $provider->issueCard($request);

        $card = VirtualCard::create([
            'virtual_card_request_id' => $request->id,
            'wallet_id'               => $request->wallet_id,
            'user_id'                 => $request->user_id,
            'provider_id'             => $request->provider_id,
            'network'                 => $request->network,
            'provider_card_id'        => $cardInfo['id'],
            'last4'                   => $cardInfo['last4'],
            'brand'                   => $cardInfo['brand'],
            'expiry_month'            => $cardInfo['expiry_month'],
            'expiry_year'             => $cardInfo['expiry_year'],
            'status'                  => $cardInfo['status'],
            'meta'                    => $cardInfo['meta'],
        ]);

        $request->update([
            'status'             => VirtualCardRequestStatus::Issued,
            'provider_issued_at' => now(),
            'provider_response'  => $cardInfo['raw'] ?? null,
        ]);

        return $card;
    }

    /**
     * Top up a virtual card.
     *
     * @throws Exception
     */
    public function topup(int $cardId, float $amount): string
    {
        $user        = Auth::user();
        $virtualCard = VirtualCard::where('id', $cardId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $wallet     = $virtualCard->wallet;
        $feeSetting = $this->getFeeSetting($virtualCard, 'topup');
        $this->validateAmountRange($feeSetting, $amount);
        $fee   = $feeSetting ? (float) $feeSetting->calculateFee($amount) : 0.0;
        $total = $amount + $fee;

        // Check wallet balance
        if ($wallet->balance < $total) {
            throw new NotifyErrorException(__('Insufficient wallet balance.'));
        }

        $provider      = $this->providerFactory->getProvider($virtualCard->provider->code);
        $topUpResponse = $provider->topUpCard($amount, $virtualCard->provider_card_id);

        $details = [
            'trxData'       => $topUpResponse,
            'amount'        => $amount,
            'charge'        => $fee,
            'netAmount'     => $amount,
            'payableAmount' => $total,
        ];

        $this->createTransactionData($details, $virtualCard->provider, $wallet, TrxType::CARD_TOPUP);

        return 'success';
    }

    /**
     * Withdraw from a virtual card.
     *
     * @throws Exception
     */
    public function withdraw(int $cardId, float $amount): string
    {
        $user        = Auth::user();
        $virtualCard = VirtualCard::where('id', $cardId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $wallet     = $virtualCard->wallet;
        $feeSetting = $this->getFeeSetting($virtualCard, 'withdraw');
        $this->validateAmountRange($feeSetting, $amount);
        $fee   = $feeSetting ? $feeSetting->calculateFee($amount) : 0.0;
        $total = $amount + $fee;

        $provider = $this->providerFactory->getProvider($virtualCard->provider->code);

        $withdrawResponse = $provider->withdrawFromCard($amount, $virtualCard->provider_card_id);

        $details = [
            'trxData'       => $withdrawResponse,
            'amount'        => $amount,
            'charge'        => $fee,
            'netAmount'     => $amount,
            'payableAmount' => $total,
        ];
        $this->createTransactionData($details, $virtualCard->provider, $wallet, TrxType::CARD_WITHDRAW);

        return 'success';
    }

    /**
     * Create transaction data for logging and further processing.
     */
    protected function createTransactionData(array $details, VirtualCardProvider $provider, Wallet $wallet, TrxType $trxType)
    {
        $trxData = new TransactionData(
            user_id: Auth::id(),
            trx_type: $trxType,
            amount: $details['amount'],
            amount_flow: $trxType === TrxType::CARD_TOPUP ? AmountFlow::PLUS : AmountFlow::MINUS,
            fee: $details['charge'],
            provider: $provider->name,
            processing_type: MethodType::AUTOMATIC,
            net_amount: $details['netAmount'],
            payable_amount: $details['payableAmount'],
            payable_currency: $wallet->currency->code, // Use wallet's currency, not provider's
            wallet_reference: $wallet->uuid,
            trx_data: $details['trxData'] ?? null,
            description: __(':type via :method', ['type' => $trxType->value, 'method' => $provider->name]),
            status: TrxStatus::PENDING
        );
        Transaction::create($trxData);
    }

    /**
     * Get the fee setting for the operation (topup/withdraw).
     */
    private function getFeeSetting(VirtualCard $virtualCard, string $operation): ?VirtualCardFeeSetting
    {
        return VirtualCardFeeSetting::where('provider_id', $virtualCard->provider_id)
            ->where('currency_id', $virtualCard->wallet->currency_id)
            ->where('operation', $operation)
            ->first();
    }

    /**
     * Validate if the amount is within the allowed range.
     *
     * @throws Exception
     */
    private function validateAmountRange(?VirtualCardFeeSetting $feeSetting, float $amount): void
    {
        if ($feeSetting) {
            if ($amount < $feeSetting->min_amount || $amount > $feeSetting->max_amount) {
                throw new Exception(__('Amount must be between :min and :max', [
                    'min' => $feeSetting->min_amount,
                    'max' => $feeSetting->max_amount,
                ]));
            }
        }
    }
}
