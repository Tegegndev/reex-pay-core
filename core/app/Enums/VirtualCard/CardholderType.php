<?php

declare(strict_types=1);

namespace App\Enums\VirtualCard;

enum CardholderType: string
{
    case PERSONAL = 'personal';
    case BUSINESS = 'business';

    public function label(): string
    {
        return match ($this) {
            self::PERSONAL => __('Personal'),
            self::BUSINESS => __('Business'),
        };
    }

    public function class()
    {
        return match ($this) {
            self::PERSONAL => 'secondary',
            self::BUSINESS => 'success',
        };
    }

    public static function options(): array
    {
        return [
            self::PERSONAL->value => self::PERSONAL->label(),
            self::BUSINESS->value => self::BUSINESS->label(),
        ];
    }

    public function isBusiness(): bool
    {
        return $this === self::BUSINESS;
    }

    public function isPersonal(): bool
    {
        return $this === self::PERSONAL;
    }
}
