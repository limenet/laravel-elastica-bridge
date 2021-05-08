<?php

namespace Limenet\LaravelElasticaBridge;

use Illuminate\Support\Facades\Facade;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;

/**
 * @see \Limenet\LaravelElasticaBridge\LaravelElasticaBridge
 */
class LaravelElasticaBridgeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ElasticaClient::class;
    }
}
