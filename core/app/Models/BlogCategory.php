<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status'];

    protected $casts = [
        'name'   => 'array',
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Booted Model Events
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->slug ?? $category->name[app()->getDefaultLocale()] ?? Str::random(10));
        });

        static::saved(function () {
            self::flushCache();
        });

        static::deleted(function () {
            self::flushCache();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getNameTextAttribute(): string
    {
        $locale = app()->getLocale();

        return $this->name[$locale] ?? collect($this->name)->first() ?? '';
    }

    public function getBlogsCountAttribute(): int
    {
        return $this->blogs()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Caching Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get all active blog categories cached.
     */
    public static function activeCached()
    {
        return Cache::rememberForever('blog_categories_active', function () {
            return self::where('status', true)
                ->withCount('blogs')
                ->orderBy('name->'.app()->getLocale(), 'asc')
                ->get();
        });
    }

    /**
     * Clear blog category caches.
     */
    public static function flushCache(): void
    {
        Cache::forget('blog_categories_active');
        Cache::forget('blogs_active');

        self::with('blogs')->get()->each(function ($category) {
            $category->blogs->pluck('slug')->each(function ($slug) {
                Cache::forget('blog_slug_'.md5($slug));
            });
        });
    }
}
