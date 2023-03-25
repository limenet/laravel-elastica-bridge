<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
        ];
    }
}
