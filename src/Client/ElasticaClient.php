<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Client;

use Elastica\Client;
use Elastica\Index;

class ElasticaClient
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'host' => config('elastica-bridge.elasticsearch.host'),
            'port' => config('elastica-bridge.elasticsearch.port'),
        ]);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getIndex(string $name): Index
    {
        return $this->client->getIndex($name);
    }
}
