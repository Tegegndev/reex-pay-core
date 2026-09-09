<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'user_id',
        'referred_user_id',
        'parent_referral_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function parentReferral()
    {
        return $this->belongsTo(Referral::class, 'parent_referral_id');
    }

    public function childReferrals()
    {
        return $this->hasMany(Referral::class, 'parent_referral_id');
    }
}
