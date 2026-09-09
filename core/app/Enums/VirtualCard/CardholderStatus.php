<?php

declare(strict_types=1);

namespace App\Enums\VirtualCard;

enum CardholderStatus: string
{
    case PENDING   = 'pending';
    case APPROVED  = 'approved';
    case REJECTED  = 'rejected';
    case SUSPENDED = 'suspended';
    case INACTIVE  = 'inactive';

    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => __(str_replace('_', ' ', ucfirst($case->value))), self::cases())
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING   => __('Pending'),
            self::APPROVED  => __('Approved'),
            self::REJECTED  => __('Rejected'),
            self::SUSPENDED => __('Suspended'),
            self::INACTIVE  => __('Inactive'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING   => 'primary',
            self::APPROVED  => 'success',
            self::REJECTED  => 'danger',
            self::SUSPENDED => 'secondary',
            self::INACTIVE  => 'dark',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }
}
