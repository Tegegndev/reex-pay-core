<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawAccount extends Model
{
    protected $table = 'withdraw_accounts';

    protected $fillable = [
        'user_id',
        'withdraw_method_id',
        'name',
        'credentials',
    ];

    protected $casts = [
        'credentials' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawMethod()
    {
        return $this->belongsTo(WithdrawMethod::class, 'withdraw_method_id')->withDefault();
    }
}
