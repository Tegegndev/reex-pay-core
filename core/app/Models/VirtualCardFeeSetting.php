<?php

namespace App\Models;

use App\Enums\FixPctType;
use App\Enums\VirtualCard\VirtualCardFeeOperation;
use Illuminate\Database\Eloquent\Model;

class VirtualCardFeeSetting extends Model
{
    protected $fillable = [
        'provider_id',
        'currency_id',
        'operation',
        'fee_type',
        'fee_amount',
        'min_amount',
        'max_amount',
        'daily_txn_limit',
        'daily_amount_limit',
        'approval_threshold',
        'active',
    ];

    protected $casts = [
        'operation' => VirtualCardFeeOperation::class,
        'fee_type'  => FixPctType::class,
    ];

    // Relationship with provider
    public function provider()
    {
        return $this->belongsTo(VirtualCardProvider::class, 'provider_id');
    }

    // Relationship with currency
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    // Calculate transaction fee
    public function calculateFee($amount)
    {
        if ($this->fee_type === 'percent') {
            return ($amount * $this->fee_amount) / 100;
        }

        return $this->fee_amount;
    }

    // Check if admin approval is required
    public function requiresAdminApproval($amount)
    {
        return $this->approval_threshold !== null && $amount > $this->approval_threshold;
    }
}
