<?php

namespace Database\Factories;

use App\Models\Gears;
use Illuminate\Database\Eloquent\Factories\Factory;

class GearsFactory extends Factory
{
    protected $model = Gears::class;

    public function definition()
    {
        return [
            'tipo' => $this->faker->randomElement(['Manual', 'Automático']),
        ];
    }
}