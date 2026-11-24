<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Elastica\Client;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeFacade;

final class FacadeTest extends TestCase
{
    public function test_facade(): void
    {
        $this->assertInstanceOf(Client::class, LaravelElasticaBridgeFacade::getClient());
    }
}
