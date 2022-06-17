<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeFacade;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        LaravelElasticaBridgeFacade::disableEventListener();

        Customer::factory()->count(50)->create();
        Product::factory()->count(50)->create();

        LaravelElasticaBridgeFacade::enableEventListener();
    }
}
