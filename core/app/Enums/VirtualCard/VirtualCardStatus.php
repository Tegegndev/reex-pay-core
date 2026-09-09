<?php

namespace App\Enums\VirtualCard;

enum VirtualCardStatus: string
{
    case Active   = 'active';
    case Pending  = 'pending';
    case Inactive = 'inactive';
    case Blocked  = 'blocked';
    case Expired  = 'expired';

    // Optional: human-friendly label method
    public function label(): string
    {
        return match ($this) {
            self::Active   => __('Active'),
            self::Pending  => __('Pending'),
            self::Inactive => __('Inactive'),
            self::Blocked  => __('Blocked'),
            self::Expired  => __('Expired'),
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => __(str_replace('_', ' ', ucfirst($case->value))), self::cases())
        );
    }

    // Optional: badge color for blade/view
    public function badgeColor(): string
    {
        return match ($this) {
            self::Active   => 'success',
            self::Pending  => 'info',
            self::Inactive => 'warning',
            self::Blocked  => 'danger',
            self::Expired  => 'secondary',
        };
    }
}
