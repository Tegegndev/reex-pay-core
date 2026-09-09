<?php

namespace App\Models;

use App\Enums\ComponentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageComponent extends Model
{
    use HasFactory;

    protected $table = 'page_components';

    protected $fillable = [
        'component_icon',
        'component_name',
        'component_key',
        'content_data',
        'type',
        'is_active',
    ];

    protected $casts = [
        'content_data'     => 'array',
        'type'             => ComponentType::class,
        'repeated_content' => 'boolean',
        'is_protected'     => 'boolean',
        'is_active'        => 'boolean',
    ];

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |--------------------------------------------------------------------------
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
    public function getSectionNameAttribute(): string
    {
        if ($this->type === ComponentType::Dynamic) {
            return $this->type->value;
        }

        return $this->component_key;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    public function repeatedContents()
    {
        return $this->hasMany(PageComponentRepeatedContent::class, 'component_id');
    }

    public function limitRepeatedContentsOver(): bool
    {
        if (! $this->component_key) {
            return false;
        }

        $componentKey = strtolower($this->component_key);
        $cacheKey     = "component_definition_{$componentKey}";

        $definition = cache()->rememberForever($cacheKey, function () use ($componentKey) {
            $file = resource_path("structure/page_component/{$componentKey}.php");

            return file_exists($file) ? include $file : [];
        });

        $limit = $definition['repeated_content_limit'] ?? null;

        return is_numeric($limit) && $limit > 0 && $this->repeatedContents()->count() >= (int) $limit;
    }

    /*
     |--------------------------------------------------------------------------
     | Helper Methods
     |--------------------------------------------------------------------------
     */

    public static function contentFields($name, $type = 'component_fields'): array
    {
        $file = resource_path('structure/page_component/'.strtolower($name).'.php');

        if (! file_exists($file)) {
            \Log::warning("Component structure not found: $file");

            return [];
        }

        $definition = include $file;

        return $definition[$type] ?? (is_array($definition) ? $definition : []);
    }

    /*
     |--------------------------------------------------------------------------
     | Model Events - Auto Cache Clear
     |--------------------------------------------------------------------------
     */

    protected static function booted()
    {
        static::saved(fn (PageComponent $component) => $component->flushRelatedPagesCache());
        static::deleted(fn (PageComponent $component) => $component->flushRelatedPagesCache());
    }

    /**
     * Flush cache of all Pages where this component is used.
     */
    public function flushRelatedPagesCache(): void
    {
        $pages = Page::whereJsonContains('component_ids', (string) $this->id)->pluck('id', 'slug');

        foreach ($pages as $slug => $pageId) {
            Cache::forget("page_components_{$pageId}");
            Cache::forget('page_slug_'.md5($slug));
        }
    }
}
