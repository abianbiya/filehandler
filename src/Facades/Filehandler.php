<?php

namespace Abianbiya\Filehandler\Facades;

use Illuminate\Support\Facades\Facade;

class Filehandler extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'filehandler';
    }
}
