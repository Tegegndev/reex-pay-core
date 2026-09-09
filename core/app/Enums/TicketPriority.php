<?php

namespace App\Enums;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Foundation\Application;
use ValueError;

enum TicketPriority: string
{
    case LOW    = 'low';
    case MEDIUM = 'medium';
    case HIGH   = 'high';
    case URGENT = 'urgent';

    /**
     * Get human-readable label
     */
    public function label(): Application|array|string|Translator|null
    {
        return match ($this) {
            self::LOW    => __('Low'),
            self::MEDIUM => __('Medium'),
            self::HIGH   => __('High'),
            self::URGENT => __('Urgent'),
        };
    }

    /**
     * Get badge color (Bootstrap-like)
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::LOW    => 'secondary',
            self::MEDIUM => 'info',
            self::HIGH   => 'warning',
            self::URGENT => 'danger',
        };
    }

    /**
     * Get priority icon name (optional)
     */
    public function icon(): string
    {
        return match ($this) {
            self::LOW    => 'circle',
            self::MEDIUM => 'exclamation-circle',
            self::HIGH   => 'triangle-exclamation',
            self::URGENT => 'bolt',
        };
    }

    /**
     * All priority options as array (for forms/dropdowns)
     */
    public static function options(): array
    {
        return array_map(
            fn ($priority) => [
                'value' => $priority->value,
                'label' => $priority->label(),
            ],
            self::cases()
        );
    }

    /**
     * Get priority from string safely
     */
    public static function fromSafe(string $value): ?self
    {
        try {
            return self::from($value);
        } catch (ValueError) {
            return null;
        }
    }
}
