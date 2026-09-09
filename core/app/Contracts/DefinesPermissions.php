<?php

namespace App\Contracts;

interface DefinesPermissions
{
    /**
     * Permission mapping method
     */
    public static function permissions(): array;
}
