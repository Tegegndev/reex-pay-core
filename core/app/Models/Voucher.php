<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Voucher extends Model
{
    protected $fillable = [
        'user_id', 'code', 'amount', 'currency_id', 'is_active',
        'redeemed_by', 'redeemed_wallet_id', 'redeemed_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'is_active'   => 'boolean',
        'amount'      => 'float',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Boot the model and attach creating event to auto-generate code.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($voucher) {
            if (! $voucher->code) {
                do {
                    $voucher->code = 'VCR-'.strtoupper(Str::random(8)); // Prefix + 8-character code
                } while (self::where('code', $voucher->code)->exists());
            }
        });
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function isValid(): bool
    {
        return $this->is_active
            && is_null($this->redeemed_at);
    }
}
