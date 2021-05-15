<?php

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Illuminate\Database\Eloquent\Factories\Factory;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeServiceProvider;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\Database\Seeders\DatabaseSeeder;
use Orchestra\Testbench\TestCase as Orchestra;
use SetupTables;

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
            $this->customerIndex::class=>$this->customerIndex,
            $this->productIndex::class=>$this->productIndex,
        ]);
    }
}
