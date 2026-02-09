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
        $entityType = EntityType::inRandomOrder()->first() ?? EntityType::factory()->create();

        return [
            'id_usuario' => User::factory(),
            'id_entidad' => $entityType->id,
            'nombre_contacto' => $this->faker->name(),
            'telefono'        => $this->faker->unique()->phoneNumber(),
        ];
    }
}
