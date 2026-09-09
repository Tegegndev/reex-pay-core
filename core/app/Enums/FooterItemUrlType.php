<?php

namespace App\Enums;

enum FooterItemUrlType: string
{
    case NONE         = 'none';
    case EXTERNAL_URL = 'external_url';
    case PAGE         = 'page';
    case SOCIAL       = 'social';

    /**
     * Dropdown select options
     */
    public static function options(): array
    {
        return [
            self::NONE,
            self::EXTERNAL_URL,
            self::PAGE,
            self::SOCIAL,
        ];
    }

    /**
     * Get readable label for each type.
     */
    public function label(): string
    {
        return match ($this) {
            self::NONE         => __('None'),
            self::EXTERNAL_URL => __('External URL'),
            self::PAGE         => __('Internal Page'),
            self::SOCIAL       => __('Social Link'),
        };
    }

    /**
     * Bootstrap Badge Color Class
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::NONE         => 'secondary',
            self::EXTERNAL_URL => 'info',
            self::PAGE         => 'primary',
            self::SOCIAL       => 'success',
        };
    }

    /**
     * Resolve the actual URL based on the item relation.
     */
    public static function getResolvedUrl(\App\Models\FooterItem $item): string
    {
        return match ($item->url_type) {
            self::EXTERNAL_URL => $item->url ?? '',
            self::PAGE         => $item->relationLoaded('page') ? url($item->page->slug ?? '') : '',
            self::SOCIAL       => $item->relationLoaded('social') ? ($item->social->url ?? '') : '',
            default            => '',
        };
    }

    /**
     * Get dynamic label for admin listing or showing.
     */
    public static function getDynamicLabel(\App\Models\FooterItem $item): string
    {
        return match ($item->url_type) {
            self::EXTERNAL_URL => $item->url,
            self::PAGE         => $item->relationLoaded('page') ? ($item->page->label ?? '') : '',
            self::SOCIAL       => $item->relationLoaded('social') ? ($item->social->name ?? '') : '',
            default            => '',
        };
    }

    /**
     * Get social icon if exists
     */
    public static function getIconByValue(\App\Models\FooterItem $item): string
    {
        return $item->relationLoaded('social') ? ($item->social->icon ?? '') : '';
    }
}
