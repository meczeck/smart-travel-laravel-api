<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusCompany>
 */
class BusCompanyFactory extends Factory
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
            'name' => $this->faker->company,
            'phone_one' => $this->faker->phoneNumber,
            'phone_two' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'logo' => 'company-logos/img_65214bf0cb6a0.webp',
            'description' => $this->faker->paragraph,
            'policy' => $this->faker->paragraph,
            'business_licence' => 'business-licences/img_65214bf0cb8ed.webp',
            'registrar_id' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}