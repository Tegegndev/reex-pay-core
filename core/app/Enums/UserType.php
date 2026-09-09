<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case USER  = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => __('Admin'),
            self::USER  => __('User'),
        };
    }

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_combine(
            self::all(),
            array_map(fn (self $case) => $case->label(), self::cases())
        );
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'warning',
            self::USER  => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ADMIN => 'user-cog',
            self::USER  => 'user',
        };
    }
}
