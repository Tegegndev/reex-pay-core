<?php

namespace App\Models;

use App\Constants\CurrencyRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HigherOrderCollectionProxy;

class Wallet extends Model
{
    use HasFactory;

    /**
     * @var HigherOrderCollectionProxy|mixed
     */
    protected $table = 'wallets';

    protected $with = ['currency'];

    protected $appends = ['name', 'is_sender', 'is_receiver', 'is_payment', 'is_withdraw'];

    protected $fillable = [
        'currency_id',
        'user_id',
        'uuid',
        'balance',
        'status',
    ];

    protected $casts = [
        'currency_id' => 'integer',
        'user_id'     => 'integer',
        'balance'     => 'float',
        'uuid'        => 'string',
        'status'      => 'boolean',
    ];

    public function scopeActive($query, $role = null)
    {

        if ($role) {
            return $query->whereHas('currency', function ($query) use ($role) {
                $query->whereHas('roles', function ($query) use ($role) {
                    $query->where('role_name', $role)->where('is_active', true);
                });
            });
        }

        return $query->where('status', true)
            ->whereHas('currency', fn ($currencyQuery) => $currencyQuery->where('code', 'UGX')->where('status', true));
    }

    public function scope($query)
    {
        return $query->where('status', false);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'wallet_reference', 'uuid');
    }

    public function getLatestTransactionAttribute()
    {
        return $this->transactions()->latest('created_at')->first();
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    public function getNameAttribute(): string
    {
        return "{$this->currency->code}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCurrencyRoleInfo($role)
    {
        return $this->currency->getRoleInfo($role);
    }

    public function hasCurrencyRole(string $role): bool
    {
        return $this->currency->hasRole($role);
    }

    public function getIsSenderAttribute(): bool
    {
        return $this->hasCurrencyRole(CurrencyRole::SENDER);
    }

    public function getIsRequestMoneyAttribute(): bool
    {
        return $this->hasCurrencyRole(CurrencyRole::REQUEST_MONEY);
    }

    public function getIsPaymentAttribute(): bool
    {
        return $this->hasCurrencyRole(CurrencyRole::PAYMENT);
    }

    public function getIsWithdrawAttribute(): bool
    {
        return $this->hasCurrencyRole(CurrencyRole::WITHDRAW);
    }

    public function supportedPaymentMethods($currency)
    {
        return DepositMethod::active()->where('currency', $currency)->get();
    }

    public function virtualCardRequests()
    {
        return $this->hasMany(VirtualCardRequest::class);
    }

    public function virtualCards()
    {
        return $this->hasMany(VirtualCard::class);
    }
}
