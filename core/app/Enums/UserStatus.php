<?php

namespace App\Enums;

enum UserStatus: int
{
    case ACTIVE   = 1;
    case INACTIVE = 0;

    public static function options(): array
    {
        return array_combine(
            self::all(),
            array_map(fn (self $case) => $case->label(), self::cases())
        );
    }

    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE   => __('Active'),
            self::INACTIVE => __('Inactive'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE   => 'success',
            self::INACTIVE => 'danger',
        };
    }
}
