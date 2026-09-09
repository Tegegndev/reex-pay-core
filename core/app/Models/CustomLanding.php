<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomLanding extends Model
{
    protected $fillable = ['name', 'folder', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public static function getActiveLanding()
    {
        return self::where('status', true)->first();
    }
}
