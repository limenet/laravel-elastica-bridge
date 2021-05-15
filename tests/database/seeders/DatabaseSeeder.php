<?php

namespace Limenet\LaravelElasticaBridge\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Customer::factory()
                ->count(50)
                ->create();
    }
}
