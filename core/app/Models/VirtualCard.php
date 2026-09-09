<?php

namespace App\Models;

use App\Enums\VirtualCard\VirtualCardNetwork;
use App\Enums\VirtualCard\VirtualCardStatus;
use Illuminate\Database\Eloquent\Model;

class VirtualCard extends Model
{
    protected $guarded = [];

    protected $casts = [
        'meta'    => 'array',
        'network' => VirtualCardNetwork::class,
        'status'  => VirtualCardStatus::class,
    ];

    // Hide provider_card_id from JSON if you want
    protected $hidden = ['provider_card_id', 'meta'];

    /**
     * Get the originating request for this card.
     */
    public function request()
    {
        return $this->belongsTo(VirtualCardRequest::class, 'virtual_card_request_id');
    }

    /**
     * Get the wallet that owns this card.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the user who owns this card.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(VirtualCardProvider::class);
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

    /**
     * Scope a query to filter virtual cards by search, status, and provider.
     */
    public function scopeFilter($query, $request)
    {
        // Search
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('last4', 'like', "%{$search}%")
                    ->orWhere('network', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }
        // Status filter
        if ($status = $request->status) {
            $query->where('status', $status);
        }
        // Provider filter
        if ($provider = $request->provider_id) {
            $query->where('provider_id', $provider);
        }

        return $query;
    }
}
