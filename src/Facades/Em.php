<?php namespace Brouwers\LaravelDoctrine\Facades;

use Illuminate\Support\Facades\Facade;

class Em extends Facade
{

    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'em';
    }
}
