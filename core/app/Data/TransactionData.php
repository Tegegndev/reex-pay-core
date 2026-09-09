<?php

namespace App\Data;

use App\Enums\AmountFlow;
use App\Enums\MethodType;
use App\Enums\TrxStatus;
use App\Enums\TrxType;

class TransactionData
{
    public int $user_id;

    public TrxType $trx_type;

    public float $amount;

    public AmountFlow $amount_flow;

    public ?float $fee;

    public ?string $currency;

    public ?string $provider;

    public MethodType $processing_type;

    public ?float $net_amount;

    public ?float $payable_amount;

    public ?string $payable_currency;

    public ?string $wallet_reference;

    public ?string $trx_reference;

    public ?array $trx_data;

    public ?string $remarks;

    public ?string $description;

    public TrxStatus $status;

    public ?string $trx_token;

    public ?\DateTimeInterface $expires_at;

    /**
     * Constructor to initialize transaction data aligned with the transactions table schema.
     */
    public function __construct(
        int $user_id,
        TrxType $trx_type,
        float $amount,
        AmountFlow $amount_flow = AmountFlow::DEFAULT,
        ?float $fee = 0,
        ?string $currency = null,
        ?string $provider = null,
        MethodType $processing_type = MethodType::AUTOMATIC,
        ?float $net_amount = 0,
        ?float $payable_amount = null,
        ?string $payable_currency = null,
        ?string $wallet_reference = null,
        ?string $trx_reference = null,
        ?array $trx_data = null,
        ?string $remarks = null,
        ?string $description = '',
        TrxStatus $status = TrxStatus::PENDING,
        ?string $trx_token = null,
        ?\DateTimeInterface $expires_at = null,

    ) {
        $this->user_id          = $user_id;
        $this->trx_type         = $trx_type;
        $this->amount           = $amount;
        $this->amount_flow      = $amount_flow;
        $this->fee              = $fee;
        $this->currency         = $currency ?? config('app.default_currency');
        $this->provider         = $provider;
        $this->processing_type  = $processing_type;
        $this->net_amount       = $net_amount;
        $this->payable_amount   = $payable_amount;
        $this->payable_currency = $payable_currency;
        $this->wallet_reference = $wallet_reference;
        $this->trx_reference    = $trx_reference;
        $this->trx_data         = $trx_data;
        $this->remarks          = $remarks;
        $this->description      = $description;
        $this->status           = $status;
        $this->trx_token        = $trx_token;
        $this->expires_at       = $expires_at;
    }

    /**
     * Creates a copy of the current TransactionData with overridden properties.
     *
     * @param array $overrides Associative array of properties to override.
     */
    public function copy(array $overrides = []): TransactionData
    {
        $properties       = get_object_vars($this); // Get all current properties
        $mergedProperties = array_merge($properties, $overrides); // Merge overrides

        return new self(...array_values($mergedProperties));
    }
}
