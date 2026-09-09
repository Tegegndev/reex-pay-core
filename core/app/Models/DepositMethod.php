<?php

namespace App\Models;

use App\Enums\FixPctType;
use App\Enums\MethodType;
use App\Services\CurrencyConversionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DepositMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_gateway_id',
        'icon',
        'name',
        'type',
        'code',
        'currency',
        'currency_symbol',
        'min_limit',
        'max_limit',
        'rate_type',
        'rate',
        'charge_type',
        'charge',
        'user_charge',
        'user_charge_type',
        'merchant_charge',
        'merchant_charge_type',
        'fields',
        'notes',
        'status',
    ];

    protected $casts = [
        'type'                 => MethodType::class,
        'user_charge_type'     => FixPctType::class,
        'merchant_charge_type' => FixPctType::class,
        'payment_gateway_id'   => 'integer',
        'fields'               => 'array',
        'status'               => 'boolean',
        'min_limit'            => 'float',
        'max_limit'            => 'float',
        'rate'                 => 'float',
        'charge'               => 'float',
        'user_charge'          => 'double',
        'merchant_charge'      => 'double',
    ];

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true)
            ->where(function ($q) {
                $q->where('type', 'manual')
                    ->orWhereHas('paymentGateway', fn ($gatewayQuery) => $gatewayQuery->where('status', true));
            });
    }

    public function scopeGetByCode($query, $code)
    {
        return $query->where('code', $code)->first();
    }

    public static function getByCode($code)
    {
        return Cache::rememberForever('deposit_method_'.$code, function () use ($code) {
            return self::where('code', $code)->first();
        });
    }

    public function getLogoAltAttribute(): string
    {
        if ($this->attributes['type'] !== MethodType::AUTOMATIC->value) {
            return $this->attributes['icon'] ?? '';
        }

    return $this->attributes['icon'] ?? $this->paymentGateway?->logo ?? '';
    }

    public function getMethodCodeAttribute(): ?string
    {
        return $this->attributes['code'] ?? $this->attributes['method_code'] ?? null;
    }
    public function setMethodCodeAttribute($value): void { $this->attributes['code'] = $value; }
    public function getMinDepositAttribute(): float
    {
        return (float) ($this->attributes['min_deposit'] ?? $this->attributes['min_limit'] ?? 0);
    }

    public function getMaxDepositAttribute(): float
    {
        return (float) ($this->attributes['max_deposit'] ?? $this->attributes['max_limit'] ?? 0);
    }
    public function getConversionRateAttribute(): string
    {
        return number_format((float) ($this->attributes['rate'] ?? $this->attributes['conversion_rate'] ?? 1), 2, '.', '');
    }

    /**
     * Get the appropriate charge based on current user type.
     * Returns user_charge for regular users, merchant_charge for merchants.
     * Falls back to original charge field if user-specific charges are not set.
     */
    public function getChargeAttribute(): float
    {
        $user = auth()->user();

        // If no authenticated user, return original charge
        if (! $user) {
            return (float) $this->attributes['charge'];
        }

        // Check if authenticated user is from User model (not Admin model)
        // Admin access should always return original charge
        if (! ($user instanceof \App\Models\User)) {
            return (float) $this->attributes['charge'];
        }

        // Check if user is merchant
        $isMerchant = $this->isUserMerchant($user);

        if ($isMerchant && $this->attributes['merchant_charge'] !== null) {
            return (float) $this->attributes['merchant_charge'];
        }

        if (! $isMerchant && $this->attributes['user_charge'] !== null) {
            return (float) $this->attributes['user_charge'];
        }

        // Fallback to original charge if user-specific charges are not set
        return (float) $this->attributes['charge'];
    }

    /**
     * Get the appropriate charge type based on current user type.
     * Returns user_charge_type for regular users, merchant_charge_type for merchants.
     * Falls back to original charge_type field if user-specific charge types are not set.
     */
    public function getChargeTypeAttribute(): string
    {
        $user = auth()->user();

        // If no authenticated user, return original charge_type
        if (! $user) {
            return $this->attributes['charge_type'];
        }

        // Check if authenticated user is from User model (not Admin model)
        // Admin access should always return original charge_type
        if (! ($user instanceof \App\Models\User)) {
            return $this->attributes['charge_type'];
        }

        // Check if user is merchant
        $isMerchant = $this->isUserMerchant($user);

        if ($isMerchant && $this->attributes['merchant_charge_type'] !== null) {
            return $this->attributes['merchant_charge_type'];
        }

        if (! $isMerchant && $this->attributes['user_charge_type'] !== null) {
            return $this->attributes['user_charge_type'];
        }

        // Fallback to original charge_type if user-specific charge types are not set
        return $this->attributes['charge_type'];
    }

    /**
     * Check if the given user is a merchant.
     * Uses the existing isMerchant() method from User model.
     */
    private function isUserMerchant($user): bool
    {
        return $user->isMerchant();
    }

    /**
     * Calculate the total charge for a given amount.
     * Now uses dynamic charge and charge_type based on user context.
     */
    public function calculateCharge(float $amount): float
    {
        $chargeType = $this->charge_type; // This will use the dynamic accessor
        $charge     = $this->charge; // This will use the dynamic accessor

        if ($chargeType === 'fixed') {
            return $charge;
        }

        if ($chargeType === 'percent') {
            return ($charge / 100) * $amount;
        }

        return 0.0;
    }

    /**
     * The "booted" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Flush the cache when a deposit method is deleted.
        static::deleted(function ($model) {
            Cache::forget('deposit_method_'.$model->code);
        });

        // Flush the cache when a deposit method is updated.
        static::updated(function ($model) {
            Cache::forget('deposit_method_'.$model->code);
        });

        // Flush the cache when a deposit method is created.
        static::created(function ($model) {
            Cache::forget('deposit_method_'.$model->code);
        });
    }
}
