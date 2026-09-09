<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralContent extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'heading',
        'positive_guidelines',
        'negative_guidelines',
        'image_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'heading'             => 'array',
        'positive_guidelines' => 'array',
        'negative_guidelines' => 'array',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    /**
     * Get the referral content (always returns single instance).
     */
    public static function getContent(): self
    {
        $content = static::latest()->first();

        if (! $content) {
            // Auto-create default content if none exists
            $content = static::create([
                'heading' => [
                    'en' => 'Share your unique referral link and earn for every successful signup.',
                ],
                'positive_guidelines' => [
                    'en' => [
                        'Easily share the link on social media platforms.',
                        'Promote your link through any marketing channel.',
                    ],
                ],
                'negative_guidelines' => [
                    'en' => [
                        'Multiple accounts from the same device are not allowed.',
                        'Automated signups using bots are prohibited.',
                    ],
                ],
                'image_path' => 'general/static/svg/gift.svg',
            ]);
        }

        return $content;
    }

    /**
     * Get localized heading.
     */
    public function getLocalizedHeading(?string $locale = null): string
    {
        $locale  = $locale ?? app()->getLocale();
        $heading = $this->heading;

        if (is_array($heading)) {
            return $heading[$locale] ?? $heading[app()->getDefaultLocale()] ?? '';
        }

        return $heading ?? '';
    }

    /**
     * Get localized positive guidelines.
     */
    public function getLocalizedPositiveGuidelines(?string $locale = null): array
    {
        $locale     = $locale ?? app()->getLocale();
        $guidelines = $this->positive_guidelines;

        if (is_array($guidelines) && isset($guidelines[$locale])) {
            return $guidelines[$locale];
        }

        if (is_array($guidelines) && isset($guidelines[app()->getDefaultLocale()])) {
            return $guidelines[app()->getDefaultLocale()];
        }

        return is_array($guidelines) ? $guidelines : [];
    }

    /**
     * Get localized negative guidelines.
     */
    public function getLocalizedNegativeGuidelines(?string $locale = null): array
    {
        $locale     = $locale ?? app()->getLocale();
        $guidelines = $this->negative_guidelines;

        if (is_array($guidelines) && isset($guidelines[$locale])) {
            return $guidelines[$locale];
        }

        if (is_array($guidelines) && isset($guidelines[app()->getDefaultLocale()])) {
            return $guidelines[app()->getDefaultLocale()];
        }

        return is_array($guidelines) ? $guidelines : [];
    }
}
