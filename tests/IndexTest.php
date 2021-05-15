<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Elastica\Client;
use Elastica\Index;
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
    public function raw_index()
    {
        $index = $this->customerIndex->getElasticaIndex();
        $this->assertInstanceOf(Index::class, $index);
        $this->assertSame($this->customerIndex->getName(),$index->getName());
    }

    /** @test */
    public function settings()
    {
        $settings = $this->customerIndex->getCreateArguments();
        $mappings = $this->customerIndex->getMapping();
        $this->assertTrue($this->customerIndex->hasMapping());
            $this->assertArrayHasKey('mappings', $settings);
            $this->assertSame($settings['mappings'],$mappings);
    }
}
