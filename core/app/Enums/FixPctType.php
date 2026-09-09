<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Fee type for virtual card operations: Fixed or Percent
 */
enum FixPctType: string
{
    case FIXED   = 'fixed';
    case PERCENT = 'percent';

    /**
     * Get the display label for the enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::FIXED   => __('Fixed'),
            self::PERCENT => __('Percent'),
        };
    }

    /**
     * Get the symbol for the enum value (for UI, e.g. %, currency symbol).
     */
    public function symbol(): string
    {
        return match ($this) {
            self::FIXED   => siteCurrency('symbol'),
            self::PERCENT => '%',
        };
    }

    /**
     * Get all types as key => label array for select options.
     */
    public static function options(): array
    {
        return [
            self::FIXED->value   => siteCurrency('symbol'),
            self::PERCENT->value => '%',
        ];
    }

    /**
     * Check if this enum instance is percent
     */
    public function isPercent(): bool
    {
        return $this === self::PERCENT;
    }

    /**
     * Check if this enum instance is fixed
     */
    public function isFixed(): bool
    {
        return $this === self::FIXED;
    }

    /**
     * Check if the given type is percent (static method for backward compatibility)
     */
    public static function isPercentStatic($type): bool
    {
        return $type === self::PERCENT->value;
    }

    /**
     * Check if the given type is fixed (static method for backward compatibility)
     */
    public static function isFixedStatic($type): bool
    {
        return $type === self::FIXED->value;
    }
}
