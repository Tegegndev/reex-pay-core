<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class IpInfoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ifinfo.service'; // Key that matches the service binding
    }
}
