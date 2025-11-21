<?php

namespace Database\Factories;

use App\Models\Buyers;
use App\Models\Sellers;
use App\Models\Cars;
use App\Models\Sales;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesFactory extends Factory
{
    protected $model = Sales::class;

    public function definition(): array
    {
        // Creamos un coche para sacar el precio
        $car = Cars::factory()->create();

        return [
            'id_comprador' => Buyers::factory(),
            'id_vendedor' => Sellers::factory(),
            'id_vehiculo' => $car->id,
            'precio' => $car->precio,
        ];
    }
}
