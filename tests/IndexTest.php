<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Elastica\Index;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use RuntimeException;

class IndexTest extends TestCase
{
    protected CustomerIndex $customerIndex;

    public function setUp():void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
    }
    /** @test */
    public function raw_index()
    {
        $index = $this->customerIndex->getElasticaIndex();

        $this->assertInstanceOf(Index::class, $index);
        $this->assertSame($this->customerIndex->getName(), $index->getName());
    }

    /** @test */
    public function settings()
    {
        $settings = $this->customerIndex->getCreateArguments();
        $mappings = $this->customerIndex->getMapping();

        $this->assertTrue($this->customerIndex->hasMapping());
        $this->assertArrayHasKey('mappings', $settings);
        $this->assertSame($settings['mappings'], $mappings);
    }

    /** @test */
    public function document_to_model()
    {
        Customer::all()
            ->each(function (Customer $customer):void {
                $document = $customer->toElasticaDocument($this->customerIndex);
                $model = $this->customerIndex->getModelInstance($document);
                $this->assertInstanceOf(Customer::class, $model);
                $this->assertSame($customer->id, $model->id);
            });
    }
    /** @test */
    public function empty_document_to_model()
    {
        /** @var Customer $customer */
        $customer = Customer::first();
        $document = $customer->toElasticaDocument($this->customerIndex);
        $document->setId(null);

        $this->expectException(RuntimeException::class);
        $this->customerIndex->getModelInstance($document);
    }
    /** @test */
    public function blue_green()
    {
        $this->assertFalse($this->customerIndex->hasBlueGreenIndices());
        $this->expectException(BlueGreenIndicesIncorrectlySetupException::class);
        $this->customerIndex->getBlueGreenActiveElasticaIndex();
    }
}
