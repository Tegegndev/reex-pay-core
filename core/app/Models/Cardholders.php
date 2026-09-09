<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use App\Enums\KycStatus;
use App\Enums\VirtualCard\CardholderStatus;
use App\Enums\VirtualCard\CardholderType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cardholders extends Model
{
    protected $table = 'cardholders';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'gender',
        'dob',
        'relation',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'card_type',
        'businesses_id',
        'kyc_status',
        'kyc_type',
        'address_proof_type',
        'kyc_documents',
        'note',
        'status',
    ];

    protected $casts = [
        'kyc_documents' => 'array',
        'dob'           => 'date',
        'status'        => CardholderStatus::class,
        'kyc_status'    => KycStatus::class,
        'card_type'     => CardholderType::class,
        'gender'        => Gender::class,
    ];

    // Cardholder type enum accessor
    public function cardType(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? CardholderType::from($value) : CardholderType::PERSONAL,
            set: fn ($value) => $value instanceof CardholderType ? $value->value : $value,
        );
    }

    // Business relation (only if card_type = business)
    public function business(): BelongsTo
    {
        return $this->belongsTo(Businesses::class, 'businesses_id');
    }

    // User relation
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kycTemplate()
    {
        return $this->belongsTo(KycTemplate::class, 'kyc_type');
    }

    // Full name accessor
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

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

    public function getBusinessAddressAttribute($value)
    {
        return $this->business ? $this->business->full_address : $value;
    }

    // ===== Query Scopes =====
    public function scopeStatus($query, $status)
    {
        if ($status) {
            $query->where('status', $status);
        }
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%")
                    ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%"))
                    ->orWhereHas('business', fn ($b) => $b->where('business_name', 'like', "%$search%"));
            });
        }
    }
    // Optionally: scopeDateRange for future
}
