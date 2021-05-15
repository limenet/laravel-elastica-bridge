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

class FeatureTest extends TestCase
{

    /** @test */
    public function index_repository()
    {
        $this->assertCount(2, $this->indexRepository->all());
        $this->assertInstanceOf(CustomerIndex::class, $this->indexRepository->get($this->customerIndex::class));
    }
}
