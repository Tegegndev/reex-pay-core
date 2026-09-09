<?php

namespace App\Constants;

/**
 * Class TimeUnits
 *
 * This class provides constants and methods for working with time units.
 */ class TimeUnits
{
    /**
     * Constant for 1 minute in minutes.
     */
    public const MINUTE = 1; // 1 minute

    /**
     * Constant for 1 hour in minutes.
     */
    public const HOUR = 60;  // 60 minutes = 1 hour

    /**
     * Constant for 1 day in minutes.
     */
    public const DAY = 1440; // 1440 minutes = 1 day

    /**
     * Returns an array of all time units with their corresponding names.
     */
    public static function getAll(): array
    {
        return [
            self::MINUTE => self::getUnitName(self::MINUTE),
            self::HOUR   => self::getUnitName(self::HOUR),
            self::DAY    => self::getUnitName(self::DAY),
        ];
    }

    /**
     * Returns the name of a time unit.
     *
     * @param  int    $unit The time unit.
     * @return string The name of the time unit.
     */
    public static function getUnitName(int $unit): string
    {
        return match ($unit) {
            self::MINUTE => __('Minute'),
            self::HOUR   => __('Hour'),
            self::DAY    => __('Day'),
            default      => '',
        };
    }

    /**
     * Returns the value of a time unit in minutes.
     *
     * @param  int $unit The time unit.
     * @return int The value of the time unit in minutes.
     */
    public static function getUnitValue(int $unit): int
    {
        return match ($unit) {
            self::MINUTE => 1,
            self::HOUR   => 60,
            self::DAY    => 1440,
            default      => 0,
        };
    }
}
