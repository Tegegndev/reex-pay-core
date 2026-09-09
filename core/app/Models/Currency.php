<?php

namespace App\Models;

use App\Services\CurrencyConversionService;
use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'flag',
        'name',
        'code',
        'symbol',
        'type',
        'exchange_rate',
        'rate_live',
        'auto_wallet',
        'default',
        'status',
    ];

    protected $casts = [
        'rate_live'     => 'boolean',
        'exchange_rate' => 'float',
        'default'       => 'boolean',
        'status'        => 'boolean',
    ];

    /**
     * Get the default currency.
     */
    public static function getDefault(): ?Currency
    {
        return static::where('code', 'UGX')->where('status', true)->first()
            ?? new self([
                'code' => 'UGX',
                'symbol' => 'UGX',
                'name' => 'Ugandan Shilling',
                'exchange_rate' => 1,
                'default' => true,
                'status' => true,
            ]);
    }

    public function isDefault(): bool
    {
        return static::where('default', true)->exists();
    }

    public static function autoWallets()
    {
        return static::where('code', 'UGX')
            ->where('status', true)
            ->where(function ($query) {
                $query->where('auto_wallet', true)->orWhere('default', true);
            })
            ->get();
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(CurrencyRole::class);
    }

    public function activeRoles(): HasMany
    {
        return $this->hasMany(CurrencyRole::class)->where('is_active', true);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->activeRoles->pluck('role_name')->contains($roleName);
    }

    public function getRoleInfo($roleName)
    {
        $role = $this->activeRoles->where('role_name', $roleName)->first();

        return $role->only(['role_name', 'min_limit', 'max_limit', 'fee_type', 'fee']);

    }

    public function getExchangeRateAttribute(): string
    {
        // Check if the conversion rate is set to live.
        if ($this->attributes['rate_live']) {

            // Get an instance of the currency conversion service.
            $currencyConversionService = new CurrencyConversionService;

            // Convert the currency and ensure it returns a valid float.
            $conversionRate = $currencyConversionService->convertCurrency(1, siteCurrency(), $this->attributes['code']);

            // Check if the conversion rate is null and provide a fallback value if needed.
            if ($conversionRate === null) {
                return $this->attributes['exchange_rate'];
            }

            // Format the conversion rate to two decimal places.
            return number_format($conversionRate, 2, '.', '');

        }

        return $this->attributes['exchange_rate'];
    }

    protected static function boot()
    {
        parent::boot();

        // Clear currency cache after saving a currency.
        static::saved(function () {
            app(CurrencyService::class)->clearCurrencyCache();
        });

        // Clear currency cache after deleting a currency.
        static::deleted(function () {
            app(CurrencyService::class)->clearCurrencyCache();
        });
    }
}
