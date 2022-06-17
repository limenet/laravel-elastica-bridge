<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge;

use Illuminate\Support\Facades\Facade;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;

class LaravelElasticaBridgeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ElasticaClient::class;
    }
}
