<?php

namespace App\Constants;

class RewardType
{
    public const DEPOSIT = 'deposit';

    public const PAYMENT = 'payment';

    public const TYPES = [
        self::DEPOSIT => 'Deposit',
        self::PAYMENT => 'Payment',
    ];

    public static function getTypes(): array
    {
        return self::TYPES;
    }
}
