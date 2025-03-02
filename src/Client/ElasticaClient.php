<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Client;

use Elastica\Client;
use Elastica\Index;
use Limenet\LaravelElasticaBridge\Logging\SentryBreadcrumbLogger;

class ElasticaClient
{
    private Client $client;

    private static ?bool $listenToEvents = null;

    public function __construct()
    {
        $client = new Client([
            'host' => config('elastica-bridge.elasticsearch.host'),
            'port' => config('elastica-bridge.elasticsearch.port'),
        ]);

        if (config('elastica-bridge.logging.sentry_breadcrumbs') === true && class_exists('\Sentry\Breadcrumb')) {
            $client->setLogger(new SentryBreadcrumbLogger);
        }

        $this->client = $client;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getIndex(string $name): Index
    {
        return $this->client->getIndex($name);
    }

    public function enableEventListener(): void
    {
        self::$listenToEvents = true;
    }

    public function disableEventListener(): void
    {
        self::$listenToEvents = false;
    }

    public function listensToEvents(): bool
    {
        return self::$listenToEvents ?? config('elastica-bridge.events.listen', true);
    }
}
