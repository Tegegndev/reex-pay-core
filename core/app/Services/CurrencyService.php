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
                $currency = Currency::where('default', true)->where('status', true)->first(['code', 'symbol'])
                    ?? Currency::where('status', true)->first(['code', 'symbol']);
                return $currency ? $currency->toArray() : ['code' => 'USD', 'symbol' => '$'];
            });
        } catch (\Exception $e) {
            return ['code' => 'USD', 'symbol' => '$'];
        }
    }

    public function exists($currencyCode): bool
    {
        try {
            return Currency::where('code', $currencyCode)->where('status', true)->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get a list of all active currencies, cached.
     */
    public function getAllCurrencies()
    {
        try {
            return Cache::remember(self::ALL_CURRENCIES_CACHE_KEY, now()->addDay(), function () {
                return Currency::where('status', true)->get();
            });
        } catch (\Exception $e) {
            return collect([new Currency(['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar', 'status' => true])]);
        }
    }

    public function getCurrencyByCode($code): ?Currency
    {
        try {
            return Cache::remember("currency_by_code_{$code}", now()->addDay(), function () use ($code) {
                return Currency::where('code', $code)->where('status', true)->first();
            });
        } catch (\Exception $e) {
            return Currency::where('code', $code)->first();
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
