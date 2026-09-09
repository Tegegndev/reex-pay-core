<?php

namespace App\Models;

use App\Enums\FooterSectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FooterSection extends Model
{
    protected $fillable = ['title', 'type', 'status', 'order'];

    protected $casts = [
        'title' => 'array',
        'type'  => FooterSectionType::class,
    ];

    public function getTitleTextAttribute(): string
    {
        return $this->title[app()->getLocale()] ?? $this->title[config('app.fallback_locale')] ?? '';
    }

    public function getItemsCountAttribute(): string
    {
        return $this->items()->count() ?? 0;
    }

    public function items()
    {
        return $this->hasMany(FooterItem::class)->orderBy('order');
    }

    // ===========================================
    // 🔥 Optimized Caching Methods
    // ===========================================

    /**
     * Get all Footer Sections with cache.
     */
    public static function activeCached()
    {
        return Cache::rememberForever('footer_sections_all', function () {
            return self::where('status', 1)
                ->with(['items', 'items.page', 'items.social'])
                ->orderBy('order')
                ->get();
        });
    }

    /**
     * Clear the cache (after save, update, delete).
     */
    public static function clearCache(): void
    {
        Cache::forget('footer_sections_all');
    }

    // ➡ Auto clear cache after save/update/delete
    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }
}
