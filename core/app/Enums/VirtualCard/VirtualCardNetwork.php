<?php

namespace App\Enums\VirtualCard;

enum VirtualCardNetwork: string
{
    case Mastercard = 'mastercard';
    case Visa       = 'visa';

    // Display-friendly label
    public function label(): string
    {
        return match ($this) {
            self::Mastercard => 'Mastercard',
            self::Visa       => 'Visa',
        };
    }

    // Optionally: FontAwesome icon or SVG name
    public function icon(): string
    {
        return match ($this) {
            self::Mastercard => 'fa-brands fa-cc-mastercard',
            self::Visa       => 'fa-brands fa-cc-visa',
        };
    }
}
