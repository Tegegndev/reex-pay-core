<?php

namespace App\Enums;

use ValueError;

enum TrxType: string
{
    case DEPOSIT          = 'deposit';
    case SEND_MONEY       = 'send_money';
    case RECEIVE_MONEY    = 'receive_money';
    case REQUEST_MONEY    = 'request_money';
    case EXCHANGE_MONEY   = 'exchange_money';
    case VOUCHER          = 'voucher';
    case PAYMENT          = 'payment';
    case RECEIVE_PAYMENT  = 'receive_payment';
    case ADD_BALANCE      = 'add_balance';
    case SUBTRACT_BALANCE = 'subtract_balance';
    case WITHDRAW         = 'withdraw';
    case REFERRAL_REWARD  = 'referral_reward';
    case REWARD           = 'reward';
    case CARD_TOPUP       = 'card_topup';
    case CARD_WITHDRAW    = 'card_withdraw';

    /**
     * Get all transaction types as an array for dropdowns.
     */
    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => __(str_replace('_', ' ', ucfirst($case->value))), self::cases())
        );
    }

    public function label()
    {
        return match ($this) {
            self::DEPOSIT          => __('Deposit'),
            self::SEND_MONEY       => __('Send Money'),
            self::RECEIVE_MONEY    => __('Receive Money'),
            self::REQUEST_MONEY    => __('Request Money'),
            self::EXCHANGE_MONEY   => __('Exchange Money'),
            self::VOUCHER          => __('Voucher'),
            self::PAYMENT          => __('Payment'),
            self::RECEIVE_PAYMENT  => __('Receive Payment'),
            self::ADD_BALANCE      => __('Add Balance'),
            self::SUBTRACT_BALANCE => __('Subtract Balance'),
            self::WITHDRAW         => __('Withdraw'),
            self::REFERRAL_REWARD  => __('Referral Reward'),
            self::REWARD           => __('Reward'),
            self::CARD_TOPUP       => __('Card Topup'),
            self::CARD_WITHDRAW    => __('Card Withdraw'),
            default                => __('Unknown'),
        };
    }

    /**
     * Convert the enum value to a kebab-case (hyphenated) string.
     */
    public function kebabCase(): string
    {
        return str_replace('_', '-', $this->value);
    }

    /**
     * Returns the badge color for the current transaction type.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::DEPOSIT, self::ADD_BALANCE , self::CARD_TOPUP => 'info',
            self::RECEIVE_MONEY, self::WITHDRAW , self::CARD_WITHDRAW => 'primary',
            self::REQUEST_MONEY  => 'danger',
            self::EXCHANGE_MONEY => 'success',
            self::PAYMENT        => 'warning',
            default              => 'secondary',
        };
    }

    /**
     * Accepts a string or an array (uses the first element if array)
     * and returns the badge color. Returns 'secondary' on invalid type.
     */
    public static function getBadgesColor(string|array $type): string
    {
        if (is_array($type)) {
            $type = $type[0];
        }

        try {
            return self::from($type)->badgeColor();
        } catch (ValueError) {
            return 'secondary';
        }
    }

    /**
     * Returns an array of transaction types that support user rank.
     */
    public static function userRankSupport(): array
    {
        return [
            self::DEPOSIT,
            self::SEND_MONEY,
            self::PAYMENT,
            self::REFERRAL_REWARD,
        ];
    }

    public function icon()
    {
        return match ($this) {
            self::DEPOSIT          => 'deposit',
            self::SEND_MONEY       => 'send-money',
            self::RECEIVE_MONEY    => 'receive-money',
            self::REQUEST_MONEY    => 'request-money-1',
            self::EXCHANGE_MONEY   => 'exchange-money',
            self::VOUCHER          => 'voucher',
            self::PAYMENT          => 'payment',
            self::RECEIVE_PAYMENT  => 'receive-payment-1',
            self::ADD_BALANCE      => 'add-balance',
            self::SUBTRACT_BALANCE => 'subtract-balance',
            self::WITHDRAW         => 'withdraw',
            self::REFERRAL_REWARD  => 'referral-reward',
            self::REWARD           => 'reward',
            self::CARD_TOPUP       => 'card',
            self::CARD_WITHDRAW    => 'card-approved',

            default => 'unknown',
        };
    }
}
