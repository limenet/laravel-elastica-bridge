<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Elastica\Query\MatchQuery;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

class CommandTest extends TestCase
{
    public function test_status(): void
    {
        $this->assertSame(0, $this->artisan('elastica-bridge:status')->run());
    }

    public function test_index_command_creates_index(): void
    {
        $this->assertFalse($this->productIndex->getElasticaIndex()->exists());
        $this->assertSame(0, $this->index($this->productIndex));
        $this->assertSame(0, $this->artisan('elastica-bridge:status')->run());
        $this->assertTrue($this->productIndex->getElasticaIndex()->exists());
    }

    public function test_index_command_switches_blue_green(): void
    {
        $this->index($this->productIndex);
        $activeOld = $this->productIndex->getBlueGreenActiveElasticaIndex()->getName();
        $this->index($this->productIndex);
        $this->assertNotSame($activeOld, $this->productIndex->getBlueGreenActiveElasticaIndex()->getName());
    }

    public function test_index_command_respects_should_index(): void
    {
        $this->index($this->customerIndex);

        $this->assertCount(0, $this->customerIndex->searchForElements(new MatchQuery(IndexInterface::DOCUMENT_MODEL_ID, 1)));
        $this->assertCount(1, $this->customerIndex->searchForElements(new MatchQuery(IndexInterface::DOCUMENT_MODEL_ID, 2)));
    }

    public function test_index_command_works_with_no_model_entries(): void
    {
        $this->index($this->orderIndex);
        $this->assertSame(0, $this->orderIndex->getElasticaIndex()->count());
    }
}
