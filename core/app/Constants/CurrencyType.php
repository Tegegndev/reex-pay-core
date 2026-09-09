<?php

namespace App\Constants;

class CurrencyType
{
    public const string CRYPTO = 'crypto';

    public const string FIAT = 'fiat';

    public static function getTypes(): array
    {
        return [
            self::CRYPTO,
            self::FIAT,
        ];
    }
}
