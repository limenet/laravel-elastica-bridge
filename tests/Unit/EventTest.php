<?php

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Elastica\Client;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeFacade;

class EventTest extends TestCase
{
    /** @test */
    public function event_enable()
    {
        LaravelElasticaBridgeFacade::enableEventListener();
        $this->assertTrue(LaravelElasticaBridgeFacade::listensToEvents());
    }
    /** @test */
    public function event_disable()
    {
        LaravelElasticaBridgeFacade::disableEventListener();
        $this->assertFalse(LaravelElasticaBridgeFacade::listensToEvents());
    }
}
