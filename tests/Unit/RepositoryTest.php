<?php

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;

class RepositoryTest extends TestCase
{
    protected CustomerIndex $customerIndex;
    protected ProductIndex $productIndex;
    protected IndexRepository $indexRepository;

    public function setUp():void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->productIndex = $this->app->make(ProductIndex::class);
        $this->indexRepository = $this->app->make(IndexRepository::class);
    }
    /** @test */
    public function all()
    {
        $this->assertCount(2, $this->indexRepository->all());
        foreach ($this->indexRepository->all() as $index) {
            $this->assertInstanceOf(IndexInterface::class, $index);
        }
    }
    /** @test */
    public function single()
    {
        $this->assertInstanceOf(CustomerIndex::class, $this->indexRepository->get($this->customerIndex::class));
        $this->assertInstanceOf(ProductIndex::class, $this->indexRepository->get($this->productIndex::class));
    }
}
