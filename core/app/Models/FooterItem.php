<?php

namespace App\Models;

use App\Enums\FooterItemUrlType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FooterItem extends Model
{
    protected $fillable = [
        'footer_section_id', 'label', 'content', 'url_type',
        'url', 'page_id', 'social_id', 'order', 'icon', 'status',
    ];

    protected $casts = [
        'label'    => 'array',
        'content'  => 'array',
        'url_type' => FooterItemUrlType::class,
        'status'   => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(FooterSection::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function social()
    {
        return $this->belongsTo(Social::class);
    }

    public function getDynamicLabelAttribute(): string
    {
        return FooterItemUrlType::getDynamicLabel($this);
    }

    public function getLabelTextAttribute(): string
    {
        return $this->label[app()->getLocale()] ?? $this->label[config('app.fallback_locale')] ?? '';
    }

    public function getContentTextAttribute(): string
    {
        return $this->content[app()->getLocale()] ?? $this->content[config('app.fallback_locale')] ?? '';
    }

    public function getResolvedUrlAttribute()
    {
        return FooterItemUrlType::getResolvedUrl($this);
    }

    // ===========================================
    // 🔥 Optimized Caching Methods
    // ===========================================

    /**
     * Get all Footer Items with cache.
     */
    public static function allCached()
    {
        return Cache::rememberForever('footer_items_all', function () {
            return self::with(['section', 'page'])->orderBy('order')->get();
        });
    }

    /**
     * Clear the cache (after save, update, delete).
     */
    public static function clearCache(): void
    {
        Cache::forget('footer_items_all');
        Cache::forget('footer_sections_all');
    }

    // ➡ Auto clear cache after save/update/delete
    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }
}
