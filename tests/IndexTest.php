<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;

class IndexTest extends TestCase
{
    protected CustomerIndex $customerIndex;

    public function setUp():void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
    }
    /** @test */
    public function index_settings()
    {
        $settings = $this->customerIndex->getCreateArguments();
        if ($this->customerIndex->hasMapping()) {
            $this->assertArrayHasKey('mappings', $settings);
        }
    }
}