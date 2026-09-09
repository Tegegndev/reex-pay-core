<?php

namespace App\Models;

use App\Enums\KycStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KycSubmission extends Model
{
    protected $fillable = [
        'kyc_template_id',
        'user_id',
        'submission_data',
        'status',
        'notes',
    ];

    protected $casts = [
        'submission_data' => 'array',
        'status'          => KycStatus::class,
    ];

    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('daterange'), function ($q) use ($request) {
                $dates = explode(',', $request->daterange);

                if (count($dates) === 2) {
                    $startDate = Carbon::parse(trim($dates[0]))->startOfDay();
                    $endDate   = Carbon::parse(trim($dates[1]))->endOfDay();

                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($sub) use ($search) {
                    $sub->where('notes', 'like', "%$search%")
                        ->orWhereJsonContains('submission_data', $search)
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('username', 'like', "%$search%")
                                ->orWhere('first_name', 'like', "%$search%")
                                ->orWhere('last_name', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%");
                        });
                });
            });
    }

    public function kycTemplate()
    {
        return $this->belongsTo(KycTemplate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
