<?php

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\TestCase as TestsTestCase;

class TestCase extends TestsTestCase
{
    protected CustomerIndex $customerIndex;
    protected ProductIndex $productIndex;
    protected IndexRepository $indexRepository;
    public function setUp(): void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->productIndex = $this->app->make(ProductIndex::class);
        $this->indexRepository =$this->app->make(IndexRepository::class);
    }
}
