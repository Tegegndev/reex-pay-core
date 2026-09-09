<?php

namespace App\Enums;

enum NotificationActionType: string
{
    case CREATED   = 'created';
    case APPROVED  = 'approved';
    case REJECTED  = 'rejected';
    case COMPLETED = 'completed';
    case REQUESTED = 'requested';
    case FAILED    = 'failed';
    case LOGGED    = 'logged';

    public function label(): string
    {
        return match ($this) {
            self::CREATED   => 'Created',
            self::APPROVED  => 'Approved',
            self::REJECTED  => 'Rejected',
            self::COMPLETED => 'Completed',
            self::REQUESTED => 'Requested',
            self::FAILED    => 'Failed',
            self::LOGGED    => 'Logged',
        };
    }

    public function class(): string
    {
        return match ($this) {
            self::APPROVED, self::COMPLETED => 'success',
            self::REJECTED, self::FAILED => 'danger',
            self::CREATED, self::LOGGED, self::REQUESTED => 'primary',
        };
    }

    public static function getClass(string $status): string
    {
        return self::tryFrom($status)?->class() ?? 'info';
    }
}
