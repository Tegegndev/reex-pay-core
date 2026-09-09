<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    /**
     * Get all settings, using cache for performance.
     */
    public static function getAllSettings(): Collection
    {
        return Cache::rememberForever('settings.all', function () {
            return self::all();
        });
    }

    /**
     * Check if a given key exists in settings.
     */
    public static function has(string $key): bool
    {
        return self::getAllSettings()->contains('key', $key);
    }

    /**
     * Get validation rules for all fields of a section.
     */
    public static function getValidationRules(string $section): array
    {
        return self::getDefinedFields($section)
            ->filter(fn ($field) => ! empty($field['rules']))
            ->pluck('rules', 'key')
            ->toArray();
    }

    /**
     * Get the data type for a given field in a section.
     */
    public static function getDataType(string $field, string $section): string
    {
        return self::getDefinedFields($section)->pluck('data', 'key')->get($field, 'string');
    }

    /**
     * Set a value for a key, creating or updating as needed.
     */
    public static function set(string $key, mixed $value, string $type = 'string'): mixed
    {
        $setting = self::getAllSettings()->firstWhere('key', $key);

        if ($setting) {
            $setting->update(['val' => $value, 'type' => $type]);
            self::flushCache();

            return $value;
        }

        return self::add($key, $value, $type);
    }

    /**
     * Add a new setting if it does not exist.
     */
    public static function add(string $key, mixed $value, string $type = 'string'): mixed
    {
        if (self::has($key)) {
            return self::set($key, $value, $type);
        }

        $created = self::create(['key' => $key, 'val' => $value, 'type' => $type]);
        self::flushCache();

        return $created ? $value : false;
    }

    /**
     * Remove a setting by key.
     */
    public static function remove(string $key): bool
    {
        $deleted = self::where('key', $key)->delete();
        if ($deleted) {
            self::flushCache();

            return true;
        }

        return false;
    }

    /**
     * Get the default value for a specific field in a section.
     */
    public static function getDefaultValueForField(string $field, string $section): mixed
    {
        return self::getDefinedFields($section)->pluck('value', 'key')->get($field);
    }

    /**
     * Get a setting value. Falls back to config default if not set.
     */
    public static function get(string $key, ?string $section = null, mixed $default = null): mixed
    {
        $setting = self::getAllSettings()->firstWhere('key', $key);
        if ($setting) {
            return self::castValue($setting->val, $setting->type);
        }

        return self::getDefaultValue($key, $section, $default);
    }

    /**
     * Flush the cached settings.
     */
    public static function flushCache(): void
    {
        Cache::forget('settings.all');
    }

    /**
     * Eloquent model boot: flush cache on create, update, or delete.
     */
    protected static function booted(): void
    {
        static::created(fn () => self::flushCache());
        static::updated(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }

    /**
     * Get defined fields for a section from config/settings.php.
     */
    private static function getDefinedFields(string $section): Collection
    {
        return collect(config('settings')[$section]['elements'] ?? []);
    }

    /**
     * Cast the value to its intended type.
     */
    private static function castValue(mixed $value, ?string $type): mixed
    {
        return match ($type) {
            'int', 'integer' => (int) $value,
            'bool', 'boolean' => (bool) $value,
            default => $value,
        };
    }

    /**
     * Get the default value for a key/section, falling back to $default.
     */
    private static function getDefaultValue(string $key, ?string $section, mixed $default): mixed
    {
        return $default ?? ($section ? self::getDefaultValueForField($key, $section) : null);
    }
}
