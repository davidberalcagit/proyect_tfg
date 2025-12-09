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

        $modelos = [
            'Corolla','Civic','Focus','A3','Serie 3','Clase C',
            'Polo','Clio','308','Rio','Tucson','Qashqai',
            'Megane','Yaris','Fiesta','Sportage'
        ];

        return [
            'id_marca' => $marca->id,
            'nombre'   => $this->faker->randomElement($modelos),
        ];
    }
}
