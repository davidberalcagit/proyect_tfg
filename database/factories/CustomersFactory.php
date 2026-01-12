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

        // Handle case where no users are available (though usually factory is called with user override or after user creation)
        // But for safety, let's try to find one or create one if needed, though creating one inside definition might be recursive if not careful.
        // The error was on EntityType, so let's fix that first.

        // For id_usuario, if we are calling Customers::factory()->create(['id_usuario' => ...]), this line is evaluated anyway?
        // Laravel factories evaluate definition() even if overrides are provided.
        // So we need a fallback for id_usuario too if the DB is empty.

        $user = User::doesntHave('customer')->inRandomOrder()->first();
        if (!$user) {
             $user = User::factory()->create();
        }

        return [
            'id_usuario' => $user->id,
            'id_entidad' => $entityType->id,
            'nombre_contacto' => $this->faker->name(),
            'telefono'        => $this->faker->unique()->phoneNumber(),
        ];
    }
}
