<?php

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;

class FeatureTest extends TestCase
{

    /** @test */
    public function status()
    {
       $this->assertSame(0, $this->artisan('elastica-bridge:status')->run());
    }
}
