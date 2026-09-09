<?php

namespace App\Models;

use App\Enums\LinkTarget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Navigation extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'page_id',
        'target',
        'is_active',
        'order',
    ];

    protected $casts = [
        'name'      => 'array',
        'is_active' => 'boolean',
        'target'    => LinkTarget::class,
    ];

    /**
     * Relation to linked page (if not using custom URL).
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get translated name based on app locale.
     */
    public function getLabelAttribute(): string
    {
        return $this->name[app()->getLocale()] ?? $this->name[app()->getDefaultLocale()] ?? '';
    }

    /**
     * Get actual route or external URL.
     */
    public function getUrlAttribute(): string
    {
        $slug = $this->slug ?? optional($this->page)->slug ?? '#';

        return str_starts_with($slug, '/') ? $slug : '/'.ltrim($slug, '/');
    }

    /**
     * Determine if this navigation uses a custom URL.
     */
    public function getIsCustomUrlAttribute(): bool
    {
        return ! is_null($this->slug) && is_null($this->page_id);
    }

    /**
     * Get all active navigations with caching.
     */
    public static function activeCached()
    {
        return Cache::rememberForever('active_navigations', function () {
            return self::where('is_active', true)
                ->orderBy('order')
                ->get();
        });
    }

    /**
     * Clear the cache when navigation is updated, created or deleted.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Clear the active navigations cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('active_navigations');
    }
}
