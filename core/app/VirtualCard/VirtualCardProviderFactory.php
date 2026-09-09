<?php

namespace App\VirtualCard;

use App\VirtualCard\Providers\Stripe\StripeCardProvider;
use App\VirtualCard\Providers\StroWallet\StroWalletProvider;
use App\VirtualCard\Providers\VirtualCardProviderInterface;
use Exception;

class VirtualCardProviderFactory
{
    /**
     * Resolve provider by code.
     * Example: $factory->getProvider('stripe');
     */
    public function getProvider(string $providerCode): VirtualCardProviderInterface
    {
        return match ($providerCode) {
            'stripe'     => app(StripeCardProvider::class),
            'strowallet' => app(StroWalletProvider::class),
            default      => throw new Exception("Unsupported virtual card provider: $providerCode"),
        };
    }
}
