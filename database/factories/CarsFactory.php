<?php

namespace Database\Factories;

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
        $marca=['Toyota','BMW','Mercedes','Renault','Volvo','Ford','Hyundai','Volkswagen','Tesla','Audi'];
        $combustible=['Gasolina','Diesel','Hibrido','Electrico','Gas'];
        $cambio=['Manual','Automatico'];
        $marcasModelos = [
            'Toyota' => ['Corolla',  'RAV4', 'Camry'],
            'BMW' => ['X1', 'X2','X4'],
            'Mercedes' => ['A-Class', 'C-Class', 'E-Class'],
            'Renault' => ['Clios', 'Megane'],
            'Volvo' => ['S60', 'V90'],
            'Ford' => ['Focus', 'Fiesta', 'Kuga'],
            'Hyundai' => ['i10', 'Kona', 'Tucson'],
            'Volkswagen' => ['Golf', 'Passat'],
            'Tesla' => ['Model S','Model X', 'Model Y'],
            'Audi' => ['A3', 'A4'],
        ];
        $marca = $this->faker->randomElement(array_keys($marcasModelos));
        $modelo = $this->faker->randomElement($marcasModelos[$marca]);

        return [
            "marca" => $marca,
            "modelo" => $modelo,
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
