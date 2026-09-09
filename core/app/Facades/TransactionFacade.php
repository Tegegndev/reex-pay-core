<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TransactionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'transaction.service'; // Key that matches the service binding
    }
}
