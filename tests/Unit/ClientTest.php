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

        $this->assertSame('localhost', $client->getConfig('host'));
        $this->assertEquals(9200, $client->getConfig('port'));
    }
}
