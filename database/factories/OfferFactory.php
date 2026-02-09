<?php

namespace Database\Factories;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $car = Cars::where('id_estado', 1)->inRandomOrder()->first();

        if (!$car) {
            $car = Cars::factory()->create(['id_estado' => 1]);
        }

        $buyer = Customers::where('id', '!=', $car->id_vendedor)->inRandomOrder()->first();

        if (!$buyer) {
            $buyer = Customers::factory()->create();
        }

        return [
            'id_vehiculo' => $car->id,
            'id_comprador' => $buyer->id,
            'id_vendedor' => $car->id_vendedor,
            'cantidad' => $this->faker->numberBetween($car->precio * 0.8, $car->precio * 1.1),
            'estado' => 'pending',
        ];
    }
}
