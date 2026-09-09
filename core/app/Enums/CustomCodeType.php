<?php

namespace App\Enums;

enum CustomCodeType: string
{
    case CSS = 'css';

    public function label(): string
    {
        return match ($this) {
            self::CSS => 'Custom CSS',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
