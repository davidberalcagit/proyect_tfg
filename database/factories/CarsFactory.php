<?php

namespace Database\Factories;

use App\Models\Brands;
use App\Models\Models;
use App\Models\Sellers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
        $combustible=['Gasolina','Diesel','Hibrido','Electrico','Gas'];
        $cambio=['Manual','Automatico'];


        return [
            "matricula"=>strtoupper($this->faker->bothify("####???")),
            "año_matri"=>$this -> faker -> numberBetween(2000,2025),
            "motor"=>$this -> faker -> word(),
            "combustible"=>$this -> faker -> randomElement($combustible),
            "cambio"=>$this -> faker -> randomElement($cambio),
            "color"=>$this -> faker -> colorName(),
            "km"=>$this -> faker -> numberBetween(100,100000),
            "precio"=>$this -> faker -> numberBetween(2000,100000)."€",
            "moto"=>$this -> faker -> boolean(50),
            "descripcion"=>$this -> faker -> text()
        ];
    }
}
