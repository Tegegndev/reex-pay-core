<?php

namespace App\Enums;

enum Gender: string
{
    case Male   = 'M';
    case Female = 'F';

    // Optional: For future extensibility
    case Other = 'O';

    public function label(): string
    {
        return match ($this) {
            self::Male   => __('Male'),
            self::Female => __('Female'),
            self::Other  => __('Other'),
            default      => '',
        };
    }

    // Helper for select option arrays
    public static function options(): array
    {
        return [
            self::Male->value   => self::Male->label(),
            self::Female->value => self::Female->label(),
            self::Other->value  => self::Other->label(),
        ];
    }
}
