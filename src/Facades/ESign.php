<?php

namespace ESign\Facades;

use Illuminate\Support\Facades\Facade;

class ESign extends Facade
{
    /**
     * Return the facade accessor.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'esign';
    }

    /**
     * Return the facade accessor.
     *
     * @return ESign
     */
    public static function esign()
    {
        return app('esign');
    }

}