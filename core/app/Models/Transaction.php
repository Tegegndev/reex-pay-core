<?php

namespace App\Models;

use App\Enums\AmountFlow;
use App\Enums\MethodType;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Services\QRCodeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    // Automatically load the related 'user' relationship when querying this model
    protected $with = ['user'];

    protected $appends = ['currency_symbol'];

    // Fields that can be mass-assigned
    protected $fillable = [
        'user_id',
        'trx_id',
        'trx_type',
        'description',
        'provider',
        'processing_type',
        'amount',
        'amount_flow',
        'fee',
        'currency',
        'net_amount',
        'payable_amount',
        'payable_currency',
        'wallet_reference',
        'trx_reference',
        'trx_data',
        'trx_token',
        'expires_at',
        'remarks',
        'status',
    ];

    // Attribute casting for specific fields
    protected $casts = [
        'trx_type'        => TrxType::class,
        'processing_type' => MethodType::class,
        'status'          => TrxStatus::class,
        'amount_flow'     => AmountFlow::class,
        'trx_data'        => 'array',
        'expires_at'      => 'datetime',
        'fee'             => 'float',
        'amount'          => 'float',
        'net_amount'      => 'float',
        'payable_amount'  => 'float',
    ];

    // Default attribute values
    protected $attributes = [
        'fee'      => 0,
        'status'   => TrxStatus::PENDING,
        'provider' => 'system',
    ];

    // Relationships

    /**
     * Automatically generate a unique transaction ID (trx_id) before creating a transaction.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (! $transaction->trx_id) {
                do {
                    $transaction->trx_id = 'TXN'.Str::upper(Str::random(12));
                } while (self::where('trx_id', $transaction->trx_id)->exists());
            }
        });
    }

    /**
     * Determine if the transaction QR is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    /**
     * Scope: Find transaction by QR token
     */
    public function scopeByTrxToken($query, string $token)
    {
        return $query->where('trx_token', $token);
    }

    public function getQrCodeSvgAttribute()
    {
        if (! $this->trx_token) {
            return '';
        }

        $qrService = app(QRCodeService::class);

        $url = route('payment.pay', [
            'merchant' => $this->trx_data['merchant_name'],
            'token'    => $this->trx_token,
        ]);

        return $qrService->generate($url);
    }

    /**
     * Get formatted expiry time (optional)
     */
    public function formattedExpiresAt(): ?string
    {
        return $this->expires_at ? $this->expires_at->format('Y-m-d H:i') : null;
    }

    /**
     * Transaction belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Unified filter scope for modular query building.
     */
    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('user_id', $id))
            ->when($filters['trx_type'] ?? null, fn ($q, $type) => $q->whereIn('trx_type', (array) $type))
            ->when($filters['processing_type'] ?? null, fn ($q, $processingType) => $q->where('processing_type', $processingType))
            ->when($filters['provider'] ?? null, fn ($q, $provider) => $q->where('provider', $provider))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['search'] ?? null, function ($q, $searchTerm) {
                $q->where(function ($q) use ($searchTerm) {
                    $q->where('trx_type', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhere('trx_id', 'like', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                        });
                });
            })
            ->when($filters['dateRange'] ?? null, function ($q, $dateRange) {
                $dateRange = explode(',', $dateRange);
                if (count($dateRange) !== 2) {
                    return;
                }
                [$startDate, $endDate] = $dateRange;
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            });
    }

    // Accessors

    /**
     * Count the total amount of transactions for a given type and status.
     */
    public function scopeCountAmount(Builder $query, string $transactionType, string $status): float
    {
        return $query->where('trx_type', $transactionType)->where('status', $status)->sum('amount');
    }

    // Scope to filter transactions from the last X days.
    public function scopeRecent($query, $days = 7)
    {
        return $query->whereDate('created_at', '>=', now()->subDays($days));
    }

    // Scope to filter transactions by a given status.
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope to filter transactions that are either failed or canceled.
    public function scopeFailedOrCanceled($query)
    {
        return $query->where(function ($q) {
            $q->where('status', TrxStatus::FAILED)
                ->orWhere('status', TrxStatus::CANCELED);
        });
    }

    /**
     * Format the created_at attribute to a readable format.
     */
    public function getCreatedAtTimeAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('M d Y h:i A');
    }

    public function getCurrencySymbolAttribute(): ?string
    {
        return getSymbol($this->currency);
    }
}
