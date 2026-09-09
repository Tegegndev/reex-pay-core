<?php

namespace App\Enums;

enum UserRole: string
{
    case USER     = 'user';
    case MERCHANT = 'merchant';

    public static function options(): array
    {
        return array_combine(
            self::all(),
            array_map(fn (self $case) => $case->title(), self::cases())
        );
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Get all roles as an array (for validation, dropdowns, etc.)
    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Get a human-readable title for the role.
     */
    public function title(): string
    {
        return match ($this) {
            self::USER     => __('User'),
            self::MERCHANT => __('Merchant'),
        };
    }

    public function color()
    {
        return match ($this) {
            self::USER     => 'primary',
            self::MERCHANT => 'warning',
        };
    }
}
