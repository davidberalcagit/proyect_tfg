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
            'nombre_empresa' => $this->faker->company(),
            'nif' => $this->faker->bothify('?########'),
            'direccion' => $this->faker->address(),
        ];
    }
}
