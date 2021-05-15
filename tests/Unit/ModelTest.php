<?php

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Product;

class ModelTest extends TestCase
{
    protected CustomerIndex $customerIndex;
    protected ProductIndex $productIndex;

    public function setUp():void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
        $this->productIndex = $this->app->make(ProductIndex::class);
    }
    /** @test */
    public function convert_to_elastica_document_customized()
    {
        Customer::all()
            ->filter(fn (Customer $customer):bool => $customer->shouldIndex($this->customerIndex))
            ->each(function (Customer $customer):void {
                $document = $customer->toElasticaDocument($this->customerIndex);
                $this->assertInstanceOf(Document::class, $document);

                $this->assertSame($customer->name, $document->get('name'));
                $this->assertSame($customer->email, $document->get('email'));
                $this->assertSame($customer->type, $document->get('type'));

                $this->assertStringContainsString('|'.$customer->id, $document->getId());
                $this->assertStringContainsString($customer::class . '|', $document->getId());

                $this->assertSame($customer->id, $document->get(IndexInterface::DOCUMENT_MODEL_ID));
                $this->assertSame($customer::class, $document->get(IndexInterface::DOCUMENT_MODEL_CLASS));
            });
    }
    /** @test */
    public function convert_to_elastica_document_default()
    {
        Product::all()
        ->filter(fn (Product $product):bool => $product->shouldIndex($this->productIndex))
            ->each(function (Product $product):void {
                $document = $product->toElasticaDocument($this->productIndex);
                $this->assertInstanceOf(Document::class, $document);

                $this->assertSame($product->name, $document->get('name'));

                $this->assertStringContainsString('|'.$product->id, $document->getId());
                $this->assertStringContainsString($product::class . '|', $document->getId());

                $this->assertSame($product->id, $document->get(IndexInterface::DOCUMENT_MODEL_ID));
                $this->assertSame($product::class, $document->get(IndexInterface::DOCUMENT_MODEL_CLASS));
            });
    }
}
