<?php

namespace App\Enums;

enum NotificationChannelType: string
{
    case EMAIL = 'email';
    case PUSH  = 'push';
    case SMS   = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => __('Email'),
            self::PUSH  => __('Push Notification'),
            self::SMS   => __('SMS'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EMAIL => 'primary',
            self::PUSH  => 'info',
            self::SMS   => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::EMAIL => 'fa-solid fa-envelope',
            self::PUSH  => 'fa-solid fa-bell',
            self::SMS   => 'fa-solid fa-sms',
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
}
