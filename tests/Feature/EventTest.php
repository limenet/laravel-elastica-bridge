<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeFacade;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Product;

class EventTest extends TestCase
{
    public function test_event_missing_index(): void
    {
        $this->assertFalse($this->productIndex->getElasticaIndex()->exists());
        LaravelElasticaBridgeFacade::enableEventListener();
        Product::factory()->create();
        $this->assertFalse($this->productIndex->getElasticaIndex()->exists());
    }

    public function test_event_create_disabled_listener(): void
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::disableEventListener();
        $product = Product::factory()->create();
        $this->assertNotInstanceOf(\Elastica\Document::class, $this->productIndex->getDocumentInstance($product));
    }

    public function test_event_create_enabled_listener(): void
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::enableEventListener();
        $product = Product::factory()->create();
        $document = $this->productIndex->getDocumentInstance($product);
        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame($product->id, $document->get(IndexInterface::DOCUMENT_MODEL_ID));
    }

    public function test_event_update_disabled_listener(): void
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::disableEventListener();
        $product = Product::all()->random();
        $oldName = $product->name;
        $newName = now()->getTimestamp();
        $product->name = $newName;
        $this->assertSame($oldName, $this->productIndex->getDocumentInstance($product)->get('name'));
        $this->assertNotSame($oldName, $newName);
        $product->save();
        $this->assertSame($oldName, $this->productIndex->getDocumentInstance($product)->get('name'));
        $this->assertNotSame($newName, $this->productIndex->getDocumentInstance($product)->get('name'));
    }

    public function test_event_update_enabled_listener(): void
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::enableEventListener();
        $product = Product::all()->random();
        $oldName = $product->name;
        $newName = 'doc-'.now()->getTimestamp();
        $product->name = $newName;
        $this->assertSame($oldName, $this->productIndex->getDocumentInstance($product)->get('name'));
        $this->assertNotSame($oldName, $newName);
        $product->save();
        $this->assertSame($newName, $this->productIndex->getDocumentInstance($product)->get('name'));
    }

    public function test_event_delete_disabled_listener(): void
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::disableEventListener();
        $product = Product::all()->random();
        $product->delete();
        $this->assertInstanceOf(Document::class, $this->productIndex->getDocumentInstance($product));
    }

    public function test_event_delete_enabled_listener(): void
    {
        $this->index($this->productIndex);
        LaravelElasticaBridgeFacade::enableEventListener();
        $product = Product::all()->random();
        $product->delete();
        $this->assertNotInstanceOf(\Elastica\Document::class, $this->productIndex->getDocumentInstance($product));
    }

    public function test_event_delete_model_not_in_index(): void
    {
        $this->index($this->customerIndex);
        LaravelElasticaBridgeFacade::enableEventListener();
        $customer = Customer::findOrFail(1);
        $this->assertNotInstanceOf(\Elastica\Document::class, $this->customerIndex->getDocumentInstance($customer));
        $customer->delete();
        $this->assertNull($this->customerIndex->getDocumentInstance($customer));
    }
}
