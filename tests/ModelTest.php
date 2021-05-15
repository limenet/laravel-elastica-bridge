<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;

class ModelTest extends TestCase
{
    protected CustomerIndex $customerIndex;

    public function setUp():void
    {
        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
    }
    /** @test */
    public function convert_to_elastica_document()
    {
        Customer::all()
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
}
