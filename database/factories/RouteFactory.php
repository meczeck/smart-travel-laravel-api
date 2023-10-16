<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'origin' => $this->faker->randomDigitNotZero,
            'destination' => $this->faker->randomDigitNotZero,
            'pathway' => $this->faker->firstName . " - " . $this->faker->firstName . " - " . $this->faker->firstName,
            'bus_company_id' => function (array $company) {
                return $company['bus_company_id'] ?? null;
            }

        ];
    }
}