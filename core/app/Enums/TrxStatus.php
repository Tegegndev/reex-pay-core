<?php

namespace App\Enums;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Foundation\Application;

enum TrxStatus: string
{
    case PENDING   = 'pending';
    case COMPLETED = 'completed';
    case CANCELED  = 'canceled';
    case FAILED    = 'failed';

    public function label(): Application|array|string|Translator|null
    {
        return match ($this) {
            self::PENDING   => __('Pending'),
            self::COMPLETED => __('Completed'),
            self::CANCELED  => __('Canceled'),
            self::FAILED    => __('Failed'),
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => __(str_replace('_', ' ', ucfirst($case->value))), self::cases())
        );
    }

    /**
     * Get the icon associated with the transaction status.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING   => 'fa-regular fa-clock',
            self::COMPLETED => 'fa-solid fa-check',
            self::CANCELED  => 'fa-solid fa-xmark',
            self::FAILED    => 'fa-solid fa-circle-exclamation',
        };
    }

    /**
     * Get the color associated with the transaction status.
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING   => 'warning',
            self::COMPLETED => 'success',
            self::CANCELED,
            self::FAILED => 'danger',
        };
    }

    public function colorCode(): string
    {
        return match ($this) {
            self::PENDING   => '#ffc107',  // Yellow
            self::COMPLETED => '#28a745',  // Green
            self::FAILED,
            self::CANCELED => '#dc3545',  // Red
        };
    }
}
