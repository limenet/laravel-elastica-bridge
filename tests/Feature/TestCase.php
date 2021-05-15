<?php

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected CustomerIndex $customerIndex;
    protected ProductIndex $productIndex;
    protected IndexRepository $indexRepository;
    public function setUp(): void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->productIndex = $this->app->make(ProductIndex::class);
        $this->indexRepository = new IndexRepository([
            $this->customerIndex::class => $this->customerIndex,
            $this->productIndex::class => $this->productIndex,
        ]);
    }
}
