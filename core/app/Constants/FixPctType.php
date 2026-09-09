<?php

namespace App\Constants;

class FixPctType
{
    // Define string constants without type declarations

    public const PERCENT = 'percent';

    public const FIXED = 'fixed';

    // Define an array constant with valid values (no trailing comma)

    public const TYPE = [
        self::PERCENT,
        self::FIXED,
    ];

    // Use a static method to dynamically fetch the array with symbols (no trailing comma)

    public static function getTypeWithSymbol(): array
    {
        return [
            self::PERCENT => '%',
            self::FIXED   => setting('currency_symbol'),
        ];
    }
}
