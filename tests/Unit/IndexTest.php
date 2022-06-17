<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Elastica\Index;
use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use RuntimeException;

class IndexTest extends TestCase
{
    protected CustomerIndex $customerIndex;
    protected ProductIndex $productIndex;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->productIndex = $this->app->make(ProductIndex::class);
    }

    public function test_raw_index(): void
    {
        $index = $this->customerIndex->getElasticaIndex();

        $this->assertInstanceOf(Index::class, $index);
        $this->assertSame($this->customerIndex->getName(), $index->getName());
    }

    public function test_settings_customized(): void
    {
        $settings = $this->customerIndex->getCreateArguments();
        $mappings = $this->customerIndex->getMapping();

        $this->assertTrue($this->customerIndex->hasMapping());
        $this->assertArrayHasKey('mappings', $settings);
        $this->assertArrayNotHasKey('settings', $settings);
        $this->assertSame($settings['mappings'], $mappings);
    }

    public function test_settings__default(): void
    {
        $settings = $this->productIndex->getCreateArguments();
        $mappings = $this->productIndex->getMapping();

        $this->assertFalse($this->productIndex->hasMapping());
        $this->assertEmpty($mappings);
        $this->assertEmpty($settings);
    }

    public function test_document_to_model(): void
    {
        Customer::all()
            ->each(function (Customer $customer): void {
                $document = $customer->toElasticaDocument($this->customerIndex);
                $model = $this->customerIndex->getModelInstance($document);
                $this->assertInstanceOf(Customer::class, $model);
                $this->assertSame($customer->id, $model->id);
            });
    }

    public function test_empty_document_to_model(): void
    {
        /** @var Customer $customer */
        $customer = Customer::first();
        $document = $customer->toElasticaDocument($this->customerIndex);
        $document->setId(null);

        $this->expectException(RuntimeException::class);
        $this->customerIndex->getModelInstance($document);
    }

    public function test_blue_green(): void
    {
        $this->assertFalse($this->customerIndex->hasBlueGreenIndices());
        $this->expectException(BlueGreenIndicesIncorrectlySetupException::class);
        $this->customerIndex->getBlueGreenActiveElasticaIndex();
        $this->customerIndex->getBlueGreenInactiveElasticaIndex();
    }

    public function test_results_base_case(): void
    {
        $this->assertSame([], $this->customerIndex->documentResultToElements(new ResultSet(new Response('{}'), new Query(), [])));
    }
}
