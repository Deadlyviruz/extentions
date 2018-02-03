<?php

namespace Deadlyviruz\Extentions\Facades;

use Illuminate\Support\Facades\Facade;

class Extention extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'extentions';
    }
}
