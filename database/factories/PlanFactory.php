<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plan_id' => Str::random(10),
            'name' => fake()->name(),
            'price' => fake()->randomDigit(),
            'billing_period' => 'month',
            'currency' => fake()->currencyCode(),
            'interval_count' => fake()->randomDigit(),
        ];
    }
}
