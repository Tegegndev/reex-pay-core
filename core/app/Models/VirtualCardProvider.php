<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VirtualCardProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'logo',
        'brand',
        'description',
        'supported_networks',
        'supported_currencies',
        'issue_fee',
        'min_balance',
        'status',
        'config',
        'payment_gateway_id',
        'order',
    ];

    protected $casts = [
        'supported_networks'   => 'array',
        'supported_currencies' => 'array',
        'issue_fee'            => 'float',
        'min_balance'          => 'float',
        'status'               => 'boolean',
        'config'               => 'array',
    ];

    // Relationships
    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeForNetwork(Builder $query, string $network): Builder
    {
        return $query->whereJsonContains('supported_networks', $network);
    }

    public function scopeForCurrency(Builder $query, string $currency): Builder
    {
        return $query->whereJsonContains('supported_currencies', $currency);
    }

    // Accessors
    public function getFeeFormattedAttribute(): string
    {
        return siteCurrency('symbol').number_format($this->issue_fee, 2);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset($this->logo) : asset('general/static/default/payment-gateway.png');
    }

    public function getNetworksListAttribute(): string
    {
        return is_array($this->supported_networks) ? implode(', ', array_map('ucfirst', $this->supported_networks)) : '';
    }

    public function getCurrenciesListAttribute(): string
    {
        return is_array($this->supported_currencies) ? implode(', ', $this->supported_currencies) : '';
    }

    // Caching (optional)
    public static function allCached()
    {
        return Cache::rememberForever('virtual_card_providers_all', function () {
            return self::active()->orderBy('order')->orderBy('id')->get();
        });
    }

    public static function flushCache(self $provider): void
    {
        Cache::forget('virtual_card_providers_all');
        Cache::forget("virtual_card_provider_code_{$provider->code}");
    }

    protected static function booted()
    {
        static::saved(fn (self $provider) => self::flushCache($provider));
        static::deleted(fn (self $provider) => self::flushCache($provider));
    }
}
