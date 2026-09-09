<?php

namespace App\Constants;

class Status
{
    public const TRUE = 1;

    public const FALSE = 0;

    public const ACTIVE = 1;

    public const INACTIVE = 0;

    public const ENABLE = 'enable';

    public const DISABLE = 'disable';

    public const STATUS = [
        self::TRUE  => 'Active',
        self::FALSE => 'Inactive',
    ];
}
