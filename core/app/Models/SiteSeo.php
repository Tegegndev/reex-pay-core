<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSeo extends Model
{
    protected $fillable = [
        'page_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'image',
    ];

    protected $casts = [
        'meta_title'       => 'array',
        'meta_description' => 'array',
        'meta_keywords'    => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the associated page (if not global).
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this SEO record is global (not page-specific).
     */
    public function isGlobal(): bool
    {
        return is_null($this->page_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get global SEO data with cache.
     */
    public static function global(): ?self
    {
        return Cache::rememberForever('site_global_seo', function () {
            return self::whereNull('page_id')->first();
        });
    }

    /**
     * Clear cached global SEO.
     */
    public static function clearGlobalCache(): void
    {
        Cache::forget('site_global_seo');
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    /**
     * Clear cache when SEO record is updated or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function (self $seo) {
            if ($seo->isGlobal()) {
                self::clearGlobalCache();
            }
        });

        static::deleted(function (self $seo) {
            if ($seo->isGlobal()) {
                self::clearGlobalCache();
            }
        });
    }
}
