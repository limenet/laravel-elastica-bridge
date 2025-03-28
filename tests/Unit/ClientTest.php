<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Elastica\Client;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Logging\SentryBreadcrumbLogger;
use Psr\Log\LoggerInterface;

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

        $this->assertSame('localhost', $client->getTransport()->getNodePool()->nextNode()->getUri()->getHost());
        $this->assertSame(9200, $client->getTransport()->getNodePool()->nextNode()->getUri()->getPort());

    }

    public function test_sentry_logger_not_active_by_default(): void
    {
        $client = $this->elasticaClient->getClient();

        $this->assertNotInstanceOf(SentryBreadcrumbLogger::class, $this->getLoggerProperty($client));
    }

    public function test_sentry_logger_enabled(): void
    {
        config()->set('elastica-bridge.logging.sentry_breadcrumbs', true);
        $client = (new ElasticaClient)->getClient();

        $this->assertInstanceOf(SentryBreadcrumbLogger::class, $this->getLoggerProperty($client));
    }

    private function getLoggerProperty(Client $client): LoggerInterface
    {
        $reflectedClass = new \ReflectionClass($client);
        $reflection = $reflectedClass->getProperty('_logger');
        $reflection->setAccessible(true);

        return $reflection->getValue($client);
    }
}
