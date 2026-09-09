<?php

namespace App\Enums;

enum MethodType: string
{
    case AUTOMATIC = 'auto';
    case MANUAL    = 'manual';
    case SYSTEM    = 'system';

    /**
     * Returns an array of all method types as string values.
     *
     * @return string[]
     */
    public static function types(): array
    {
        return array_map(fn (MethodType $type) => $type->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::AUTOMATIC => 'Automatic',
            self::MANUAL    => 'Manual',
            self::SYSTEM    => 'System',
        };
    }

    public function isAutomatic(): bool
    {
        return $this === self::AUTOMATIC;
    }

    public function isManual(): bool
    {
        return $this === self::MANUAL;
    }
}
