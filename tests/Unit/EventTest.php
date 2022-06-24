<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeFacade;

class EventTest extends TestCase
{
    public function test_event_enable(): void
    {
        LaravelElasticaBridgeFacade::enableEventListener();
        $this->assertTrue(LaravelElasticaBridgeFacade::listensToEvents());
    }

    public function test_event_disable(): void
    {
        LaravelElasticaBridgeFacade::disableEventListener();
        $this->assertFalse(LaravelElasticaBridgeFacade::listensToEvents());
    }
}
