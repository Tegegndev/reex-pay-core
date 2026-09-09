<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Plugin extends Model
{
    use HasFactory;

    protected $fillable = [
        'credentials',
        'fields',
        'status',
    ];

    public static function credentials($code): mixed
    {

        return Cache::rememberForever($code, function () use ($code) {
            $plugin                = self::where('code', $code)->first();
            $credentials           = json_decode($plugin->credentials, true);
            $credentials['status'] = $plugin->status;

            return $credentials;
        });
    }

    protected static function boot(): void
    {
        parent::boot();

        static::updated(function ($plugin) {
            self::flushCache($plugin->code);
        });
    }

    private static function flushCache($code): void
    {
        Cache::forget($code);
        Cache::forget('plugins_data');
    }
}
