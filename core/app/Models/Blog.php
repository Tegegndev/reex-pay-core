<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content',
        'meta_title', 'meta_description', 'meta_keywords',
        'thumbnail', 'admin_id', 'category_id', 'status',
    ];

    protected $casts = [
        'title'            => 'array',
        'excerpt'          => 'array',
        'content'          => 'array',
        'meta_title'       => 'array',
        'meta_description' => 'array',
        'status'           => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Booted Model Events
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function ($blog) {
            $blog->slug = Str::slug($blog->slug ?? $blog->title[app()->getDefaultLocale()] ?? Str::random(10));
        });

        static::saved(fn (self $blog) => $blog->flushCache());
        static::deleted(fn (self $blog) => $blog->flushCache());
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive()
    {
        return $this->where('status', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function author()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getTitleTextAttribute(): string
    {
        $locale = app()->getLocale();

        return $this->title[$locale] ?? collect($this->title)->first() ?? '';
    }

    public function getExcerptTextAttribute(): string
    {
        $locale = app()->getLocale();

        return $this->excerpt[$locale] ?? collect($this->excerpt)->first() ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */
    public static function findBySlug(string $slug): self
    {
        $slug = trim($slug);

        return Cache::rememberForever('blog_slug_'.md5($slug), function () use ($slug) {
            return self::with('category')
                ->active()
                ->where('slug', $slug)
                ->first();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Caching Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get all published blogs cached.
     */
    public static function activeCached()
    {
        return Cache::rememberForever('blogs_active', function () {
            return self::where('status', true)->with('category')->with('author')->latest()->get();
        });
    }

    /**
     * Clear blog cache.
     */
    public function flushCache(): void
    {
        $slug = trim($this->slug);
        Cache::forget('blogs_active');
        Cache::forget('blog_slug_'.md5($slug));

    }
}
