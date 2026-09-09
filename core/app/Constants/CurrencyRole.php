<?php

namespace App\Constants;

class CurrencyRole
{
    public const string SENDER = 'sender';

    public const string REQUEST_MONEY = 'request_money';

    public const string EXCHANGE = 'exchange';

    public const string VOUCHER = 'voucher';

    public const string PAYMENT = 'payment';

    public const string WITHDRAW = 'withdraw';

    public static function getRoles(): array
    {
        return [
            self::SENDER,
            self::REQUEST_MONEY,
            self::EXCHANGE,
            self::VOUCHER,
            self::PAYMENT,
            self::WITHDRAW,
        ];
    }

    public static function getBadgesColor($role): string
    {

        if (is_array($role)) {
            $role = $role[0]; // if an array, take the first role
        }
        $badgesColor = [
            self::SENDER        => 'info',
            self::REQUEST_MONEY => 'secondary',
            self::EXCHANGE      => 'primary',
            self::VOUCHER       => 'danger',
            self::PAYMENT       => 'success',
            self::WITHDRAW      => 'warning',
        ];

        return $badgesColor[$role] ?? 'secondary';
    }
}
