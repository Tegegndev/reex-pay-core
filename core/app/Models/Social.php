<?php

namespace App\Models;

use App\Enums\LinkTarget;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Social extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_class',
        'target',
        'url',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'target' => LinkTarget::class,
    ];

    // ================================
    // 🔹 Query Scope for Active Only
    // ================================
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // ================================
    // 🔥 Caching Methods
    // ================================

    /**
     * Get all socials with caching.
     */
    public static function allCached()
    {
        return Cache::rememberForever('socials_all', function () {
            return self::orderBy('id')->get();
        });
    }

    /**
     * Get active socials with caching.
     */
    public static function activeCached()
    {
        return Cache::rememberForever('socials_active', function () {
            return self::active()->orderBy('id')->get();
        });
    }

    /**
     * Clear socials cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('socials_all');
        Cache::forget('socials_active');
        Cache::forget('footer_sections_all');
    }

    // ================================
    // 🔥 Auto clear cache after save/delete
    // ================================
    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }
}
