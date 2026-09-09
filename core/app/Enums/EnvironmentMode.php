<?php

namespace App\Enums;

enum EnvironmentMode: string
{
    case SANDBOX = 'sandbox';
    case PRODUCTION = 'production';

    /**
     * Get all available environment modes
     */
    public static function options(): array
    {
        return [
            self::SANDBOX->value => __('Sandbox (Test)'),
            self::PRODUCTION->value => __('Production (Live)')
        ];
    }

    /**
     * Get environment display name
     */
    public function label(): string
    {
        return match ($this) {
            self::SANDBOX => __('Sandbox'),
            self::PRODUCTION => __('Production')
        };
    }

    /**
     * Get environment description
     */
    public function description(): string
    {
        return match ($this) {
            self::SANDBOX => __('Test environment - No real money transactions. Perfect for integration testing.'),
            self::PRODUCTION => __('Live environment - Real money transactions. Use with caution.')
        };
    }

    /**
     * Get environment icon
     */
    public function icon(): string
    {
        return match ($this) {
            self::SANDBOX => 'fas fa-flask',
            self::PRODUCTION => 'fas fa-rocket'
        };
    }

    /**
     * Get environment color class
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::SANDBOX => 'warning',
            self::PRODUCTION => 'success'
        };
    }

    /**
     * Check if environment is sandbox
     */
    public function isSandbox(): bool
    {
        return $this === self::SANDBOX;
    }

    /**
     * Check if environment is production
     */
    public function isProduction(): bool
    {
        return $this === self::PRODUCTION;
    }

    public function getLabel()
    {
        return match ($this) {
            self::SANDBOX => 'Sandbox',
            self::PRODUCTION => 'Production'
        };
    }

    public function getBadgeClass()
    {
        return match ($this) {
            self::SANDBOX => 'warning',
            self::PRODUCTION => 'success'
        };
    }

    /**
     * Get API credential prefix for environment
     */
    public function credentialPrefix(): string
    {
        return match ($this) {
            self::SANDBOX => 'test_',
            self::PRODUCTION => ''
        };
    }
}
