<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Elastica\Exception\ExceptionInterface;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Exception\BaseException;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\InvoiceIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\OrderIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\TestCase as TestsTestCase;

class TestCase extends TestsTestCase
{
    protected CustomerIndex $customerIndex;

    protected InvoiceIndex $invoiceIndex;

    protected OrderIndex $orderIndex;

    protected ProductIndex $productIndex;

    protected IndexRepository $indexRepository;

    protected ElasticaClient $elasticaClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->invoiceIndex = $this->app->make(InvoiceIndex::class);
        $this->orderIndex = $this->app->make(OrderIndex::class);
        $this->productIndex = $this->app->make(ProductIndex::class);
        $this->indexRepository = $this->app->make(IndexRepository::class);
        $this->elasticaClient = $this->app->make(ElasticaClient::class);

        $this->cleanupIndices();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanupIndices();
    }

    protected function cleanupIndices(): void
    {
        foreach ([$this->customerIndex, $this->invoiceIndex, $this->orderIndex, $this->productIndex] as $index) {
            try {
                if ($index->getElasticaIndex()->hasAlias($index->getName())) {
                    $index->getElasticaIndex()->removeAlias($index->getName());
                }

                $active = $index->getBlueGreenActiveElasticaIndex();
                $inactive = $index->getBlueGreenInactiveElasticaIndex();
                if ($active->exists()) {
                    $active->delete();
                }

                if ($inactive->exists()) {
                    $inactive->delete();
                }
            } catch (BaseException|ExceptionInterface) {
            }
        }
    }

    protected function index(IndexInterface $index): int
    {
        return $this->artisan('elastica-bridge:index', ['index' => [$index->getName()]])->run();
    }
}
