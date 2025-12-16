<?php

namespace Database\Factories;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Customers;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\Sellers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cars>
 */
class CarsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vendedor= Customers::inRandomOrder()->first();
        $marca = Brands::inRandomOrder()->first();
        $model = CarModels::where("id_marca", $marca->id)->inRandomOrder()->first();
        $marcha = Gears::inRandomOrder()->first();
        $combustible = Fuels::inRandomOrder()->first();

        return [
            "id_vendedor"=>$vendedor->id,
            "id_marca"   => $marca->id,
            "id_modelo"  => $model->id,
            "id_marcha"  => $marcha->id,
            "id_combustible" => $combustible->id,
            "matricula"=>strtoupper($this->faker->bothify("####???")),
            "año_matri"=>$this -> faker -> numberBetween(2000,2025),
            "motor"=>$this -> faker -> word(),
            "color"=>$this -> faker -> colorName(),
            "km"=>$this -> faker -> numberBetween(100,100000),
            "precio"=>$this -> faker -> numberBetween(2000,100000),
            "descripcion"=>$this -> faker -> text(),
        ];
    }
}
