<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'is_verified',
        'subscribed_at',
    ];

    protected $casts = [
        'is_verified'   => 'boolean',
        'subscribed_at' => 'datetime',
    ];
}
