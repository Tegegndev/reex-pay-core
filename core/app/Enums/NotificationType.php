<?php

namespace App\Enums;

enum NotificationType: string
{
    case DEPOSIT_USER         = 'deposit_user';
    case DEPOSIT_ACTION_USER  = 'deposit_action_user';
    case DEPOSIT_ADMIN        = 'deposit_admin';
    case DEPOSIT_ACTION_ADMIN = 'deposit_action_admin';
    case BALANCE_UPDATED_USER = 'balance_updated_user';
    case SUPPORT_USER         = 'support_user';
    case SUPPORT_ADMIN        = 'support_admin';

    /**
     * Get the icon associated with a notification type statically.
     */
    public static function getIcon(string $type): string
    {
        return self::tryFrom($type)?->icon() ?? 'info';
    }

    /**
     * Get the icon associated with a notification type.
     */
    public function icon(): string
    {
        return match ($this) {
            self::DEPOSIT_USER,
            self::DEPOSIT_ACTION_USER,
            self::DEPOSIT_ADMIN,
            self::DEPOSIT_ACTION_ADMIN => 'wallet-plus',

            self::SUPPORT_USER,
            self::SUPPORT_ADMIN => 'ticket',

            // Default fallback for any other notification type
            default => 'info',
        };
    }
}
