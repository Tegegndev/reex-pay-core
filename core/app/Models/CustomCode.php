<?php

namespace App\Models;

use App\Enums\CustomCodeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CustomCode extends Model
{
    protected $fillable = ['type', 'content', 'status'];

    protected $casts = [
        'type'   => CustomCodeType::class,
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOfType($query, CustomCodeType $type)
    {
        return $query->where('type', $type)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    */

    public static function getCached(CustomCodeType $type): ?self
    {
        return Cache::rememberForever(self::cacheKey($type), function () use ($type) {
            return self::active()->ofType($type);
        });
    }

    public function flushCache(): void
    {
        Cache::forget(self::cacheKey($this->type));
    }

    public static function cacheKey(CustomCodeType $type): string
    {
        return 'custom_code_'.$type->value;
    }

    /*
    |--------------------------------------------------------------------------
    | Model Booting
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saved(fn (self $customCode) => $customCode->flushCache());
    }
}
