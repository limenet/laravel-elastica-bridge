<?php

namespace Limenet\LaravelElasticaBridge;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Limenet\LaravelElasticaBridge\LaravelElasticaBridge
 */
class LaravelElasticaBridgeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-elastica-bridge';
    }
}
