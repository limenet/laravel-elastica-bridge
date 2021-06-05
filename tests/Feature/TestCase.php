<?php

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Elastica\Exception\ResponseException;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Exception\BaseException;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\OrderIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\TestCase as TestsTestCase;

class TestCase extends TestsTestCase
{
    protected CustomerIndex $customerIndex;
    protected OrderIndex $orderIndex;
    protected ProductIndex $productIndex;
    protected IndexRepository $indexRepository;
    protected ElasticaClient $elasticaClient;
    public function setUp(): void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->orderIndex = $this->app->make(OrderIndex::class);
        $this->productIndex = $this->app->make(ProductIndex::class);
        $this->indexRepository = $this->app->make(IndexRepository::class);
        $this->elasticaClient = $this->app->make(ElasticaClient::class);

        $this->cleanupIndices();
    }
    public function tearDown(): void
    {
        parent::tearDown();

        $this->cleanupIndices();
    }
    protected function cleanupIndices()
    {
        foreach ([$this->customerIndex, $this->orderIndex, $this->productIndex] as $index) {
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
            } catch (BaseException | ResponseException) {
            }
        }
    }

    protected function index(IndexInterface $index): int
    {
        return $this->artisan('elastica-bridge:index', ['index' => [$index->getName()]])->run();
    }
}
