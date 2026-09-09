<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class WalletFacade extends Facade
{
    /**
     * Get the registered name of the component in the container.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wallet.service'; // Key that matches the service binding
    }
}
