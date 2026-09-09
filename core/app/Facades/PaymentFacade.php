<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'payment.service'; // Key that matches the service binding
    }
}
