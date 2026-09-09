<?php

namespace App\Models;

use App\Enums\VirtualCard\VirtualCardNetwork;
use App\Enums\VirtualCard\VirtualCardRequestStatus;
use Illuminate\Database\Eloquent\Model;

class VirtualCardRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'provider_response'  => 'array',
        'admin_reviewed_at'  => 'datetime',
        'provider_issued_at' => 'datetime',
        'network'            => VirtualCardNetwork::class,
        'status'             => VirtualCardRequestStatus::class,
    ];

    public function scopeFilter($query, $request)
    {
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('uuid', 'like', "%{$search}%")
                    ->orWhereHas('wallet.currency', function ($q) use ($search) {
                        $q->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($dateRange = $request->daterange) {
            $dates = explode(',', $dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    \Carbon\Carbon::parse($dates[0])->startOfDay(),
                    \Carbon\Carbon::parse($dates[1])->endOfDay(),
                ]);
            }
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Get the wallet associated with this request.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the user who made this request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the card issued for this request.
     */
    public function card()
    {
        return $this->hasOne(VirtualCard::class);
    }

    // Helper: latest KYC status
    public function getUserKycStatusAttribute()
    {
        return $this->user->latestKycSubmission->status ?? null;
    }

    // Optional: status_label accessor
    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label() ?? '';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return $this->status?->badgeColor() ?? 'secondary';
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // 7-character random uppercase string (collision risk very low for small scale)
            $model->uuid = strtoupper(\Str::random(7));
        });
    }
}
