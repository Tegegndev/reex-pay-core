<?php

namespace App\Enums;

use Exception;

enum AmountFlow: string
{
    case PLUS  = 'plus';
    case MINUS = 'minus';

    case DEFAULT = 'default';

    /**
     * Get the corresponding Bootstrap text color class.
     *
     * @throws Exception
     */
    public function color(TrxStatus $status): string
    {
        if ($status !== TrxStatus::COMPLETED) {
            return '';
        }

        return match ($this) {
            self::PLUS    => 'text-success',
            self::MINUS   => 'text-danger',
            self::DEFAULT => '',
        };
    }

    /**
     * Get the corresponding transaction sign.
     */
    public function sign(TrxStatus $status): string
    {
        if ($status !== TrxStatus::COMPLETED) {
            return '';
        }

        return match ($this) {
            self::PLUS    => '+',
            self::MINUS   => '-',
            self::DEFAULT => '',
        };
    }
}
