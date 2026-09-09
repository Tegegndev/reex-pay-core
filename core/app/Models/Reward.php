<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = ['type', 'level', 'percentage'];

    protected $casts = [
        'percentage' => 'float',
    ];
}
