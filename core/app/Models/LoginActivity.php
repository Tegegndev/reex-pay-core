<?php

namespace App\Models;

use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LoginActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'country',
        'device',
        'browser',
        'platform',
        'user_agent',
        'login_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter login activities.
     */
    public function scopeFilter($query, Request $request)
    {
        // Filter by Date Range
        if ($request->filled('daterange')) {
            $dates = explode(',', $request->daterange);
            if (count($dates) === 2) {
                [$startDate, $endDate] = $dates;
                $query->whereBetween('login_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            }
        }

        // Filter by User Role
        if ($request->filled('type') && in_array($request->type, UserRole::all())) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->type);
            });
        }

        // Filter by Search Term (IP, User, Email, Full Name, Country, Device, Browser, Platform)
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('ip_address', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('country', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('device', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('browser', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('platform', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('username', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                    });
            });
        }

        return $query;
    }
}
