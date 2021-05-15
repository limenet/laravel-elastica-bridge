<?php

namespace Limenet\LaravelElasticaBridge\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'type' => $this->faker->randomElement(['small','medium','big']),
        ];
    }
}
