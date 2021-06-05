<?php

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Elastica\Document;
use Elastica\Query\MatchQuery;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeFacade;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Product;

class EventTest extends TestCase
{
    /** @test */
    public function event_missing_index()
    {
        $this->assertFalse($this->productIndex->getElasticaIndex()->exists());
        LaravelElasticaBridgeFacade::enableEventListener();
        Product::factory()->create();
        $this->assertFalse($this->productIndex->getElasticaIndex()->exists());
    }

    /** @test */
    public function event_create_disabled_listener()
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::disableEventListener();
        $product = Product::factory()->create();
        $this->assertNull($this->productIndex->getDocumentInstance($product));
    }

    /** @test */
    public function event_create_enabled_listener()
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::enableEventListener();
        $product = Product::factory()->create();
        $document=$this->productIndex->getDocumentInstance($product);
        $this->assertInstanceOf(Document::class,$document);
        $this->assertSame($product->id,$document->get(IndexInterface::DOCUMENT_MODEL_ID));
    }

    /** @test */
    public function event_update_disabled_listener()
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::disableEventListener();
        $product = Product::all()->random();
        $oldName = $product->name;
        $newName = time();
        $product->name = $newName;
        $this->assertSame($oldName, $this->productIndex->getDocumentInstance($product)->get('name'));
        $this->assertNotSame($oldName, $newName);
        $product->save();
        $this->assertSame($oldName, $this->productIndex->getDocumentInstance($product)->get('name'));
        $this->assertNotSame($newName, $this->productIndex->getDocumentInstance($product)->get('name'));
    }

    /** @test */
    public function event_update_enabled_listener()
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::enableEventListener();
        $product = Product::all()->random();
        $oldName = $product->name;
        $newName = time();
        $product->name = $newName;
        $this->assertSame($oldName, $this->productIndex->getDocumentInstance($product)->get('name'));
        $this->assertNotSame($oldName, $newName);
        $product->save();
        $this->assertSame($newName, $this->productIndex->getDocumentInstance($product)->get('name'));
    }

    /** @test */
    public function event_delete_disabled_listener()
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::disableEventListener();
        $product = Product::all()->random();
        $product->delete();
        $this->assertInstanceOf(Document::class,$this->productIndex->getDocumentInstance($product));
    }

    /** @test */
    public function event_delete_enabled_listener()
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::enableEventListener();
        $product = Product::all()->random();
        $product->delete();
        $this->assertNull($this->productIndex->getDocumentInstance($product));
    }

    /** @test */
    public function event_delete_model_not_in_index()
    {
        $this->index($this->customerIndex);
        LaravelElasticaBridgeFacade::enableEventListener();
        $customer = Customer::findOrFail(1);
        $this->assertNull($this->customerIndex->getDocumentInstance($customer));
        $customer->delete();
        $this->assertNull($this->customerIndex->getDocumentInstance($customer));
    }
}
