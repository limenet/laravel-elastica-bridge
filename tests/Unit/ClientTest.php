<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Limenet\LaravelElasticaBridge\Client\ElasticaClient;

class ClientTest extends TestCase
{
    protected ElasticaClient $elasticaClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->elasticaClient = $this->app->make(ElasticaClient::class);
    }

    public function test_configured_client(): void
    {
        $client = $this->elasticaClient->getClient();

        $this->assertSame(config('elastica-bridge.elasticseach.host'), $client->getConfig('host'));
        $this->assertEquals(config('elastica-bridge.elasticseach.port'), $client->getConfig('port'));
    }
}
