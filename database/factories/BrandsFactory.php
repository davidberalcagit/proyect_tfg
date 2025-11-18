<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Models>
 */
class BrandsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marca=['Toyota','BMW','Mercedes','Renault','Volvo','Ford','Hyundai','Volkswagen','Tesla','Audi'];

        return [
            "nombre"=>$marca
        ];
    }
}
