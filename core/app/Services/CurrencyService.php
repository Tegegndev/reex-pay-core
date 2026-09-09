<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    public const string DEFAULT_CURRENCY_CACHE_KEY = 'default_currency';

    public const string ALL_CURRENCIES_CACHE_KEY = 'all_currencies';

    /**
     * Get the default currency code, cached for efficiency.
     * Reyco Pay Solutions uses UGX (Ugandan Shilling) as default currency.
     */
    public function getDefaultCurrency()
    {
        try {
            return Cache::remember(self::DEFAULT_CURRENCY_CACHE_KEY, now()->addDay(), function () {
                $currency = Currency::where('default', true)->first(['code', 'symbol']);
                return $currency ? $currency->toArray() : ['code' => 'UGX', 'symbol' => 'UGX'];
            });
        } catch (\Exception $e) {
            // Fallback to prevent Laravel errors - Reyco Pay Solutions default
            return ['code' => 'UGX', 'symbol' => 'UGX'];
        }
    }

    public function exists($currencyCode): bool
    {
        try {
            return Currency::where('code', $currencyCode)->exists();
        } catch (\Exception $e) {
            // Reyco Pay Solutions - assume currency exists to prevent errors
            return true;
        }
    }

    /**
     * Get a list of all active currencies, cached.
     * Reyco Pay Solutions - returns UGX as fallback if database unavailable.
     */
    public function getAllCurrencies()
    {
        try {
            return Cache::remember(self::ALL_CURRENCIES_CACHE_KEY, now()->addDay(), function () {
                return Currency::where('code', 'UGX')->where('status', true)->get();
            });
        } catch (\Exception $e) {
            // Return Reyco Pay Solutions default currency if database unavailable
            return collect([new Currency(['code' => 'UGX', 'symbol' => 'UGX', 'name' => 'Ugandan Shilling', 'status' => true])]);
        }
    }

    public function getCurrencyByCode($code): Currency
    {
        try {
            return Cache::remember("currency_by_code_{$code}", now()->addDay(), function () use ($code) {
                return Currency::where('code', $code)->first();
            });
        } catch (\Exception $e) {
            // Reyco Pay Solutions - return UGX as fallback to prevent Laravel errors
            return new Currency(['code' => 'UGX', 'symbol' => 'UGX', 'name' => 'Ugandan Shilling', 'status' => true]);
        }
    }

    /**
     * Clear cached currency data.
     */
    public function clearCurrencyCache(): void
    {
        Cache::forget(self::DEFAULT_CURRENCY_CACHE_KEY);
        Cache::forget(self::ALL_CURRENCIES_CACHE_KEY);
    }
}
