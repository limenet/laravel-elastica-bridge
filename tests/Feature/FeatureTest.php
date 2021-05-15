<?php

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;

class FeatureTest extends TestCase
{

    /** @test */
    public function index_repository()
    {
        $this->assertCount(2, $this->indexRepository->all());
        $this->assertInstanceOf(CustomerIndex::class, $this->indexRepository->get($this->customerIndex::class));
    }
}
