<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IPBlock extends Model
{
    protected $table = 'ip_blocks';

    protected $fillable = [
        'ip_address',
        'reason',
    ];
}
