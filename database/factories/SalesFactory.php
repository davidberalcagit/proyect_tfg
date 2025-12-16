<?php

namespace Database\Factories;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Sales;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesFactory extends Factory
{
    protected $model = Sales::class;



        public function definition(): array
        {
            $car = Cars::inRandomOrder()->first();
            $vendedor = Customers::inRandomOrder()->first();
            $comprador = Customers::inRandomOrder()->first();

            // precio suelto
            $precio = $car->precio;

            return [
                'id_comprador' => $comprador->id,
                'id_vendedor' => $vendedor->id,
                'id_vehiculo' => $car->id,
                'precio'       => $car->precio,
                ];
        }
    }
