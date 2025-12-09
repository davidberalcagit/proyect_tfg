<?php

namespace Database\Factories;

use App\Models\Brands;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarModels>
 */
class BrandsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Brands::class;

    public function definition(): array
    {
        $marca=['Toyota','BMW','Mercedes','Renault','Volvo','Ford','Hyundai','Volkswagen','Tesla','Audi'];

        return [
            'nombre' => $this->faker->unique()->randomElement($marca)
        ];
    }
}
