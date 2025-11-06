<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dealerships>
 */
class DealershipsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'telefono' => $this->faker->phoneNumber(),
            'nif'=>$this->faker->numberBetween(10000000,99999999),
            'correo' => $this->faker->unique()->companyEmail(),
            'direccion' => $this->faker->address(),
        ];
    }
}
