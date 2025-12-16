<?php

namespace Database\Factories;

use App\Models\Brands;
use App\Models\CarModels;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarModelsFactory extends Factory
{
    protected $model = CarModels::class;

    public function definition(): array
    {
        $marca = Brands::inRandomOrder()->first()
            ?? Brands::factory()->create();

        $modelosPorMarca = [
            'Toyota'    => ['Corolla', 'Yaris'],
            'Honda'     => ['Civic'],
            'Ford'      => ['Focus', 'Fiesta'],
            'Audi'      => ['A3'],
            'BMW'       => ['Serie 3'],
            'Mercedes'  => ['Clase C'],
            'Volkswagen'=> ['Polo'],
            'Renault'   => ['Clio', 'Megane'],
            'Peugeot'   => ['308'],
            'Kia'       => ['Rio', 'Sportage'],
            'Hyundai'   => ['Tucson'],
            'Nissan'    => ['Qashqai'],
        ];
        $modelos = $modelosPorMarca[$marca->nombre] ?? ['Modelo Genérico'];

        return [
            'id_marca' => $marca->id,
            'nombre'   => $this->faker->randomElement($modelos),
        ];
    }
}
