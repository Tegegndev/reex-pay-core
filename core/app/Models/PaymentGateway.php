<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'name',
        'code',
        'currencies',
        'credentials',
        'is_withdraw',
        'withdraw_field',
        'status',
    ];

    protected $casts = [
        'currencies'  => 'array',
        'credentials' => 'array',
        'status'      => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true)->where('code', 'marzpay');
    }

    /**
     * Scope to only include gateways that support withdrawal.
     */
    public function scopeWithdrawAvailable(Builder $query): Builder
    {
        $column = Schema::hasColumn('payment_gateways', 'is_withdraw') ? 'is_withdraw' : 'withdraw_field';

        return $query->whereNotNull($column);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Check if withdraw field is available.
     */
    public function getWithdrawAvailableAttribute(): bool
    {
        return ! is_null($this->is_withdraw ?? $this->withdraw_field);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Fetch Methods (with optimized Caching)
    |--------------------------------------------------------------------------
    */

    /**
     * Get all payment gateways (optionally paginated).
     */
    public static function allCached()
    {
        return Cache::rememberForever('payment_gateways_all', function () {
            return self::active()->orderBy('id')->get();
        });
    }

    /**
     * Get a payment gateway by its ID.
     */
    public static function getById(int $id): ?self
    {
        return Cache::rememberForever("payment_gateway_id_{$id}", function () use ($id) {
            return self::find($id);
        });
    }

    /**
     * Get a payment gateway by its code.
     */
    public static function getByCode(string $code): ?self
    {
        return Cache::rememberForever("payment_gateway_code_{$code}", function () use ($code) {
            return self::where('code', $code)->first();
        });
    }

    /**
     * Get credentials for a specific gateway code.
     */
    public static function getCredentials(string $code): array
    {
        return self::getByCode($code)?->credentials ?? [];
    }

    /**
     * Get currencies supported by a specific gateway code.
     */
    public static function getCurrencies(string $code): array
    {
        return self::getByCode($code)?->currencies ?? [];
    }

    /*
   |--------------------------------------------------------------------------
   | Relationships
   |--------------------------------------------------------------------------
   */

    public function depositMethods(): \Illuminate\Database\Eloquent\Relations\HasMany|PaymentGateway
    {
        return $this->hasMany(DepositMethod::class, 'payment_gateway_id', 'id');
    }

    public function withdrawMethods(): \Illuminate\Database\Eloquent\Relations\HasMany|PaymentGateway
    {
        return $this->hasMany(WithdrawMethod::class, 'payment_gateway_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Management
    |--------------------------------------------------------------------------
    */

    /**
     * Flush related cache keys.
     */
    public static function flushCache(self $gateway): void
    {
        Cache::forget('payment_gateways_all');
        Cache::forget("payment_gateway_id_{$gateway->id}");
        Cache::forget("payment_gateway_code_{$gateway->code}");
    }

    /**
     * Auto-clear cache on update or delete.
     */
    protected static function booted()
    {
        static::saved(fn (self $gateway) => self::flushCache($gateway));
        static::deleted(fn (self $gateway) => self::flushCache($gateway));
    }
}
