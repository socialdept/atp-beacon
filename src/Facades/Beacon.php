<?php

namespace SocialDept\Beacon\Facades;

use Illuminate\Support\Facades\Facade;

class Beacon extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'beacon';
    }
}
