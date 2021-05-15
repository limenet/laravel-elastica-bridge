<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;

class ModelTest extends TestCase
{
    protected  CustomerIndex $customerIndex;

    public function setUp():void {

        parent::setUp();

        $this->customerIndex = $this->app->make(CustomerIndex::class);
    }
    /** @test */
    public function convert_to_elastica_document()
    {
        /** @var Customer $customer */
        $customer=Customer::first();
        $document = $customer->toElasticaDocument($this->customerIndex);
        $this->assertInstanceOf(Document::class, $document);

        $this->assertSame($customer->name,$document->get('name'));
        $this->assertSame($customer->email,$document->get('email'));
    }
}
