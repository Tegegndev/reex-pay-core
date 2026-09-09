<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'icon',
        'name',
        'transaction_amount',
        'transaction_types',
        'description',
        'reward',
        'features',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active'          => 'boolean',
        'transaction_amount' => 'integer',
        'transaction_types'  => 'array', // Casting JSON to array
        'features'           => 'array', // Casting JSON to array
        'reward'             => 'double',
    ];

    /**
     * Get the description of the rank.
     */
    public function getDescriptionAttribute($value): ?string
    {
        return $value ?? 'No description provided';
    }

    /**
     * Scope a query to only include active ranks.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
