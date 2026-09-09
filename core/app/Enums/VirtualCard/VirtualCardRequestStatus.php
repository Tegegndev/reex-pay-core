<?php

// app/Enums/VirtualCardRequestStatus.php

namespace App\Enums\VirtualCard;

enum VirtualCardRequestStatus: string
{
    case Pending       = 'pending';
    case AdminApproved = 'admin_approved';
    case Rejected      = 'rejected';
    case Issued        = 'issued';
    case Failed        = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending       => __('Pending'),
            self::AdminApproved => __('Admin Approved'),
            self::Rejected      => __('Rejected'),
            self::Issued        => __('Issued'),
            self::Failed        => __('Failed'),
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => __(str_replace('_', ' ', ucfirst($case->value))), self::cases())
        );
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending       => 'warning',
            self::AdminApproved => 'info',
            self::Rejected      => 'danger',
            self::Issued        => 'success',
            self::Failed        => 'secondary',
        };
    }
}
