<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WithdrawSchedule extends Model
{
    public $timestamps = false;

    protected $table = 'withdraw_schedules';

    protected $fillable = ['day', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', true);
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', false);
    }

    public function toggleStatus(): void
    {
        $this->update(['status' => ! $this->status]);
    }

    /**
     * Check if withdrawals are enabled for today.
     */
    public static function isWithdrawEnabledToday(): bool
    {
        $today = Carbon::now()->format('l'); // Get the current day name (e.g., 'Monday')

        return self::where('day', $today)->where('status', true)->exists();
    }
}
