<?php

namespace Vasilysmolin\smo;

use Illuminate\Support\Facades\Facade;

class SmoFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'smo';
    }
}
