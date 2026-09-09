<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KycStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Businesses extends Model
{
    protected $table = 'businesses';

    protected $fillable = [
        'user_id',
        'business_name',
        'registration_number',
        'tin',
        'business_type',
        'contact_email',
        'contact_phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'documents',
        'kyc_status',
        'status',
    ];

    protected $casts = [
        'documents'  => 'array',
        'kyc_status' => KycStatus::class,
        'status'     => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Business full address accessor
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
