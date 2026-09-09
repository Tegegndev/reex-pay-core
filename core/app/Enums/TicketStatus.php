<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN    = 'open';
    case PENDING = 'pending';
    case REPLIED = 'replied';
    case CLOSED  = 'closed';

    /**
     * Human-readable label for each status
     */
    public function label(): string
    {
        return match ($this) {
            self::OPEN    => __('Open'),
            self::PENDING => __('Pending'),
            self::REPLIED => __('Replied'),
            self::CLOSED  => __('Closed'),
        };
    }

    /**
     * Get badge color (Bootstrap-like class)
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::OPEN    => 'info',
            self::PENDING => 'warning',
            self::REPLIED => 'primary',
            self::CLOSED  => 'secondary',
        };
    }

    /**
     * Icon for visual context (e.g., for use in tables or cards)
     */
    public function icon(): string
    {
        return match ($this) {
            self::OPEN    => 'ticket-open',
            self::PENDING => 'hourglass',
            self::REPLIED => 'reply',
            self::CLOSED  => 'lock',
        };
    }

    /**
     * Return all options as array
     */
    public static function options(): array
    {
        return array_map(
            fn ($status) => ['value' => $status->value, 'label' => $status->label()],
            self::cases()
        );
    }
}
