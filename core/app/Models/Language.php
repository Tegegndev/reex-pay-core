<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'flag',
        'name',
        'code',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status'     => 'boolean',
    ];

    /**
     * Boot the model and listen for model events.
     */
    protected static function booted(): void
    {
        static::saved(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }

    /**
     * Get the default language (cached).
     */
    public static function default(): ?self
    {
        return Cache::rememberForever('default_language', function () {
            return self::where('is_default', true)->first(['code', 'is_rtl']);
        });
    }

    /**
     * Get all active languages with name and code only (cached).
     */
    public static function languageGet(): Collection
    {
        return Cache::rememberForever('languagesPluck', function () {
            return self::where('status', Status::ACTIVE)->pluck('name', 'code');
        });
    }

    /**
     * Get all active languages with full fields (cached).
     */
    public static function activeCached(): Collection
    {
        return Cache::rememberForever('languages', function () {
            return self::where('status', true)->get();
        });
    }

    /**
     * Clear all cached language data.
     */
    public static function flushCache(): void
    {
        Cache::forget('default_language');
        Cache::forget('languagesPluck');
        Cache::forget('languages');
    }
}
