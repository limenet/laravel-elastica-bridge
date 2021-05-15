<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;

class ClientTest extends TestCase
{
    protected ElasticaClient $elasticaClient;

    public function setUp():void
    {
        parent::setUp();

        $this->elasticaClient = $this->app->make(ElasticaClient::class);
    }
    /** @test */
    public function configured_client()
    {
        $client = $this->elasticaClient->getClient();

        $this->assertSame(config('elastica-bridge.elasticseach.host'), $client->getConfig('host'));
        $this->assertEquals(config('elastica-bridge.elasticseach.port'), $client->getConfig('port'));
    }
}
