<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Invoice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Limenet\LaravelElasticaBridge\Tests\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'status' => fake()->word(),
        ];
    }
}
