<?php

namespace Vonage\Laravel\Facade;

use Vonage\Client;
use Illuminate\Support\Facades\Facade;

class Vonage extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return Client::class;
    }
}