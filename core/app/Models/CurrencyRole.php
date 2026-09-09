<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRole extends Model
{
    use HasFactory;

    public $casts = [
        'is_active' => 'boolean',
        'min_limit' => 'float',
        'max_limit' => 'float',
        'fee'       => 'float',
    ];

    protected $fillable = [
        'currency_id',
        'role_name',
        'min_limit',
        'max_limit',
        'fee_type',
        'fee',
        'is_active',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
