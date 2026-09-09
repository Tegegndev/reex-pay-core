<?php

namespace App\Enums;

enum LinkTarget: string
{
    case SELF  = '_self';
    case BLANK = '_blank';

    /**
     * Get all available options for dropdowns.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    /**
     * Get human-readable label for each target.
     */
    public function label(): string
    {
        return match ($this) {
            self::SELF  => __('Same Tab'),
            self::BLANK => __('New Tab'),
        };
    }

    /**
     * Get the color class target.
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::SELF  => 'primary',
            self::BLANK => 'warning',
        };
    }
}
