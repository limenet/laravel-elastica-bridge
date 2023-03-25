<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\InvoiceIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Invoice;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Product;

class ModelTest extends TestCase
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

    public function test_convert_to_elastica_document_customized(): void
    {
        Customer::all()
            ->filter(fn (Customer $customer): bool => $customer->shouldIndex($this->customerIndex))
            ->each(function (Customer $customer): void {
                $document = $customer->toElasticaDocument($this->customerIndex);
                $this->assertInstanceOf(Document::class, $document);

                $this->assertSame($customer->name, $document->get('name'));
                $this->assertSame($customer->email, $document->get('email'));
                $this->assertSame($customer->type, $document->get('type'));

                $this->assertStringContainsString('-'.$customer->id, $document->getId());
                $this->assertStringContainsString(
                    str($customer::class)->classBasename()->lower()->append('-')->toString(),
                    $document->getId()
                );
                $this->assertMatchesRegularExpression('/[\w\d_-]/', $document->getId());

                $this->assertSame($customer->id, $document->get(IndexInterface::DOCUMENT_MODEL_ID));
                $this->assertSame($customer::class, $document->get(IndexInterface::DOCUMENT_MODEL_CLASS));
            });
    }

    public function test_convert_to_elastica_document_default(): void
    {
        Product::all()
        ->filter(fn (Product $product): bool => $product->shouldIndex($this->productIndex))
            ->each(function (Product $product): void {
                $document = $product->toElasticaDocument($this->productIndex);
                $this->assertInstanceOf(Document::class, $document);

                $this->assertSame($product->name, $document->get('name'));

                $this->assertStringContainsString('-'.$product->id, $document->getId());
                $this->assertStringContainsString(
                    str($product::class)->classBasename()->lower()->append('-')->toString(),
                    $document->getId()
                );
                $this->assertMatchesRegularExpression('/[\w\d_-]/', $document->getId());

                $this->assertSame($product->id, $document->get(IndexInterface::DOCUMENT_MODEL_ID));
                $this->assertSame($product::class, $document->get(IndexInterface::DOCUMENT_MODEL_CLASS));
            });
    }

    public function test_convert_to_elastica_documentuuid(): void
    {
        Invoice::all()
            ->filter(fn (Invoice $invoice): bool => $invoice->shouldIndex($this->invoiceIndex))
                ->each(function (Invoice $invoice): void {
                    $document = $invoice->toElasticaDocument($this->invoiceIndex);
                    $this->assertInstanceOf(Document::class, $document);

                    $this->assertStringContainsString('-'.$invoice->getKey(), $document->getId());
                    $this->assertStringContainsString(
                        str($invoice::class)->classBasename()->lower()->append('-')->toString(),
                        $document->getId()
                    );
                    $this->assertMatchesRegularExpression('/[\w\d_-]/', $document->getId());

                    $this->assertSame($invoice->uuid, $document->get(IndexInterface::DOCUMENT_MODEL_ID));
                    $this->assertSame($invoice::class, $document->get(IndexInterface::DOCUMENT_MODEL_CLASS));
                });
    }
}
