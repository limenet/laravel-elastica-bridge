<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Limenet\LaravelElasticaBridge\Tests\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'type' => fake()->randomElement(['small', 'medium', 'big']),
        ];
    }
}
