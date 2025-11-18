<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ModelsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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
        $marca=Marca::inRandomOrder()->first() ?? Marca::factory()->create();
        $modelo = $this->faker->randomElement($marcasModelos[$marca->nombre]);
        return [
            "id_marca"=>$marca,
            "nombre"=>$modelo,
        ];
    }
}
