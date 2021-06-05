<?php

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

class CommandTest extends TestCase
{
    /** @test */
    public function status()
    {
        $this->assertSame(0, $this->artisan('elastica-bridge:status')->run());
    }
    /** @test */
    public function index_command_creates_index()
    {
        $this->assertFalse($this->productIndex->getElasticaIndex()->exists());
        $this->assertSame(0, $this->index($this->productIndex));
        $this->assertSame(0, $this->artisan('elastica-bridge:status')->run());
        $this->assertTrue($this->productIndex->getElasticaIndex()->exists());
    }
    /** @test */
    public function index_command_switches_blue_green()
    {
        $this->index($this->productIndex);
        $activeOld = $this->productIndex->getBlueGreenActiveElasticaIndex()->getName();
        $this->index($this->productIndex);
        $this->assertNotSame($activeOld, $this->productIndex->getBlueGreenActiveElasticaIndex()->getName());
    }
}
