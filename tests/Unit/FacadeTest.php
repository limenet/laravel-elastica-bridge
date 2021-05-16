<?php

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Elastica\Client;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeFacade;

class FacadeTest extends TestCase
{
    /** @test */
    public function facade()
    {
       $this->assertInstanceOf(Client::class, LaravelElasticaBridgeFacade::getClient());
    }
}
