<?php

namespace Database\Factories;

use App\Models\EntityType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customers>
 */
class CustomersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'id_usuario' => User::doesntHave('customers')->inRandomOrder()->first()->id,
            'id_entidad' => EntityType::inRandomOrder()->first()->id,
            'nombre_contacto' => $this->faker->name(),
            'telefono'        => $this->faker->phoneNumber(),
        ];
    }
}
