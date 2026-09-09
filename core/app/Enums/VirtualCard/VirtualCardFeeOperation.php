<?php

namespace App\Enums\VirtualCard;

enum VirtualCardFeeOperation: string
{
    case Topup      = 'topup';
    case Withdrawal = 'withdrawal';

    public function label(): string
    {
        return match ($this) {
            self::Topup      => __('Top-up'),
            self::Withdrawal => __('Withdrawal'),
        };
    }

    public function icon(): string
    {
        // FontAwesome Pro 6.0 icon class mapping
        return match ($this) {
            self::Topup      => 'fa-duotone fa-circle-arrow-down fa-lg me-1',
            self::Withdrawal => 'fa-duotone fa-circle-arrow-up fa-lg me-1',
            default          => 'fa-duotone fa-arrows-rotate fa-lg me-1',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Topup      => 'success',
            self::Withdrawal => 'danger',
            default          => 'secondary',
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }
}
