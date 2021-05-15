<?php

namespace Limenet\LaravelElasticaBridge\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run():void
    {
        Customer::factory()
                ->count(50)
                ->create();
    }
}
