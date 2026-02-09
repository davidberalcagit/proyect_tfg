<?php

namespace Database\Factories;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\SaleStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sales>
 */
class SalesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $car = Cars::inRandomOrder()->first();

        if (!$car) {
            $car = Cars::factory()->create();
        }

        $vendedor = Customers::find($car->id_vendedor);
        if (!$vendedor) {
             $vendedor = Customers::factory()->create();
             $car->update(['id_vendedor' => $vendedor->id]);
        }

        $comprador = Customers::inRandomOrder()->where('id', '!=', $vendedor->id)->first();
        if (!$comprador) {
             $comprador = Customers::factory()->create();
        }

        $precio = $car->precio;

        $status = SaleStatus::inRandomOrder()->first();
        if (!$status) {
            $status = new SaleStatus();
            $status->nombre = 'Completada';
            $status->save();
        }

        return [
            'id_comprador' => $comprador->id,
            'id_vendedor' => $vendedor->id,
            'id_vehiculo' => $car->id,
            'precio' => $precio,
            'fecha' => $this->faker->date(),
            'metodo_pago' => $this->faker->randomElement(['Efectivo', 'Tarjeta', 'Transferencia']),
            'id_estado' => $status->id,
        ];
    }
}
