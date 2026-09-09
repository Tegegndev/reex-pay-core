<?php

namespace App\Enums;

enum KycStatus: int
{
    case PENDING  = 0;
    case APPROVED = 1;
    case REJECTED = 2;

    /**
     * Get the human-readable label for the KYC status.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING  => __('Pending'),
            self::APPROVED => __('Approved'),
            self::REJECTED => __('Rejected'),
        };
    }

    /**
     * Get the Bootstrap color class for the KYC status.
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING  => 'primary',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    /**
     * Get an array of all enum values.
     *
     * @return int[]
     */
    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Get an associative array of options for dropdowns.
     * Keys are enum values, values are the labels.
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_combine(
            self::all(),
            array_map(fn (self $case) => $case->label(), self::cases())
        );
    }
}
