<?php

namespace App\Models;

use App\Enums\PageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'component_ids',
        'type',
        'breadcrumb',
        'is_breadcrumb',
        'is_active',
    ];

    protected $casts = [
        'title'         => 'array',
        'component_ids' => 'array',
        'type'          => PageType::class,
        'is_breadcrumb' => 'boolean',
        'is_active'     => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * SEO relationship.
     */
    public function seo()
    {
        return $this->hasOne(SiteSeo::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope for active pages only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get cached components for this page.
     */
    public function getComponentsAttribute()
    {
        if (empty($this->component_ids) || ! is_array($this->component_ids)) {
            return collect();
        }

        return Cache::rememberForever($this->componentsCacheKey(), function () {
            return PageComponent::whereIn('id', $this->component_ids)->with('repeatedContents')
                ->active()
                ->orderByRaw('FIELD(id, '.implode(',', $this->component_ids).')')
                ->get();
        });
    }

    public function getIsHomeAttribute(): bool
    {
        return $this->slug === '/';
    }

    public function getIsProtectedAttribute(): bool
    {
        return $this->type === PageType::Static;
    }

    /**
     * Get clean slug (always starts with '/').
     */
    public function getCleanSlugAttribute(): string
    {
        return $this->slug === '/' ? '/' : '/'.ltrim($this->slug, '/');
    }

    public function getLabelAttribute(): string
    {
        return $this->title[app()->getLocale()] ?? $this->title[app()->getDefaultLocale()] ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Find a page by slug (with cache) or abort 404.
     */
    public static function findBySlug(string $slug): self
    {
        $slug = trim($slug, '/');
        $slug = $slug === '' ? '/' : $slug;

        return Cache::rememberForever('page_slug_'.md5($slug), function () use ($slug) {
            $page = self::with('seo')
                ->where(function ($query) use ($slug) {
                    $query->where('slug', $slug)
                        ->orWhere('slug', '/'.ltrim($slug, '/'));
                })
                ->active()
                ->first();

            abort_if(! $page, 404);

            return $page;
        });
    }

    public static function home(): self
    {
        return self::findBySlug('/');
    }

    public static function blog(): self
    {
        return self::findBySlug('blog');
    }

    public static function getSlugs()
    {
        return Cache::rememberForever('slugs_list', function () {
            return static::pluck('slug')->toArray();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Clear related caches.
     */
    public function flushCache(): void
    {
        Cache::forget($this->componentsCacheKey());
        Cache::forget('page_slug_'.md5($this->slug));
        Cache::forget('slugs_list');
    }

    /**
     * Get the cache key for this page's components.
     */
    protected function componentsCacheKey(): string
    {
        return "page_components_{$this->id}";
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(fn (self $page) => $page->flushCache());
        static::deleted(fn (self $page) => $page->flushCache());
    }
}
