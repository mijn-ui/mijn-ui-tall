<?php

namespace Mijnui\Mijnui;

use Illuminate\Support\Facades\Facade;


class Mijnui extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'mijnui';
    }
}
