<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Individuals>
 */
class IndividualsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'apellidos' => $this->faker->lastName(),
            'telefono' => $this->faker->numerify('6########'),
            'dni'=>$this->faker->numerify("########"),
            'correo' => $this->faker->unique()->safeEmail(),
        ];
    }
}
