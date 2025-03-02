<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Elastica\Index;
use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\InvoiceIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Invoice;
use RuntimeException;

class IndexTest extends TestCase
{
    protected CustomerIndex $customerIndex;

    protected InvoiceIndex $invoiceIndex;

    protected ProductIndex $productIndex;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->invoiceIndex = $this->app->make(InvoiceIndex::class);
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
        $createArguments = $this->customerIndex->getCreateArguments();

        $this->assertArrayHasKey('mappings', $createArguments);
        $this->assertArrayNotHasKey('settings', $createArguments);
        $this->assertSame(
            [
                'mappings' => [
                    'properties' => [
                        'group' => [
                            'type' => 'keyword',
                        ],
                        '__id' => [
                            'type' => 'keyword',
                        ],
                        '__class' => [
                            'type' => 'keyword',
                        ],
                    ],
                ],
            ],
            $createArguments
        );
    }

    public function test_settings_default(): void
    {
        $createArguments = $this->productIndex->getCreateArguments();
        $mappings = $this->productIndex->getMapping();

        $this->assertEmpty($mappings);
        $this->assertSame(
            [
                'mappings' => [
                    'properties' => [
                        '__id' => [
                            'type' => 'keyword',
                        ],
                        '__class' => [
                            'type' => 'keyword',
                        ],
                    ],
                ],
            ],
            $createArguments
        );
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

    public function test_document_to_model_uuid(): void
    {
        Invoice::all()
            ->each(function (Invoice $invoice): void {
                $document = $invoice->toElasticaDocument($this->invoiceIndex);
                $model = $this->invoiceIndex->getModelInstance($document);
                $this->assertInstanceOf(Invoice::class, $model);
                $this->assertSame($invoice->uuid, $model->uuid);
            });
    }

    public function test_empty_document_to_model(): void
    {
        /** @var Customer $customer */
        $customer = Customer::first();
        $document = $customer->toElasticaDocument($this->customerIndex);
        $document->remove(IndexInterface::DOCUMENT_MODEL_ID);

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
        $this->assertSame([], $this->customerIndex->documentResultToElements(new ResultSet(new Response('{}'), new Query, [])));
    }
}
