<?php

declare(strict_types=1);

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->productIndex = $this->app->make(ProductIndex::class);
        $this->indexRepository = $this->app->make(IndexRepository::class);
    }

    public function test_all(): void
    {
        $this->assertCount(4, $this->indexRepository->all());
        $this->assertContainsOnlyInstancesOf(IndexInterface::class, $this->indexRepository->all());
    }

    public function test_single(): void
    {
        $this->assertInstanceOf(CustomerIndex::class, $this->indexRepository->get($this->customerIndex::class));
        $this->assertInstanceOf(ProductIndex::class, $this->indexRepository->get($this->productIndex::class));
    }
}
