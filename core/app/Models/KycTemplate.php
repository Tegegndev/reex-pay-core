<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KycTemplate extends Model
{
    protected $fillable = [
        'title',
        'description',
        'fields',
        'applicable_to',
        'status',
    ];

    protected $casts = [
        'fields'        => 'array',
        'applicable_to' => 'array',
        'status'        => 'boolean',
    ];

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function submissions()
    {
        return $this->hasMany(KycSubmission::class);
    }
}
