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
            'dni' => $this->faker->numerify("########") . $this->faker->randomLetter(),
            'fecha_nacimiento' => $this->faker->date(),
        ];
    }
}
