<?php

namespace Database\Factories;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\RentalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalFactory extends Factory
{
    public function definition(): array
    {
        $car = Cars::whereIn('id_estado', [3, 6])->inRandomOrder()->first();

        if (!$car) {
            $car = Cars::factory()->create(['id_estado' => 3, 'id_listing_type' => 2]);
        }

        $cliente = Customers::inRandomOrder()->where('id', '!=', $car->id_vendedor)->first();
        if (!$cliente) {
             $cliente = Customers::factory()->create();
        }

        $fechaInicio = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $fechaFin = $this->faker->dateTimeBetween($fechaInicio, '+2 months');

        $days = $fechaInicio->diff($fechaFin)->days;
        $precioTotal = $days * $car->precio;

        return [
            'id_vehiculo' => $car->id,
            'id_cliente' => $cliente->id,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'precio_total' => $precioTotal,
            'id_estado' => RentalStatus::inRandomOrder()->first()->id ?? 2, // Default 2 (En espera)
        ];
    }
}
