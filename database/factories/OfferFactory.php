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
        // Find a car that is 'En venta' (id_estado = 1)
        $car = Cars::where('id_estado', 1)->inRandomOrder()->first();

        // If no car found, create one
        if (!$car) {
            $car = Cars::factory()->create(['id_estado' => 1]);
        }

        // Find a buyer who is NOT the seller of the car
        $buyer = Customers::where('id', '!=', $car->id_vendedor)->inRandomOrder()->first();

        // If no suitable buyer found, create one
        if (!$buyer) {
            $buyer = Customers::factory()->create();
        }

        return [
            'id_vehiculo' => $car->id,
            'id_comprador' => $buyer->id,
            'id_vendedor' => $car->id_vendedor,
            'cantidad' => $this->faker->numberBetween($car->precio * 0.8, $car->precio * 1.1), // Offer around the price
            'estado' => 'pending',
        ];
    }
}
