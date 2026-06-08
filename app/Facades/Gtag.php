<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Gtag extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gtag';
    }
}
