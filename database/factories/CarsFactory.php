<?php

namespace Database\Factories;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Color;
use App\Models\Customers;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
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
        $vendedor = Customers::inRandomOrder()->first();
        if (!$vendedor) {
             $vendedor = Customers::factory()->create();
        }

        $model = CarModels::inRandomOrder()->first();

        if (!$model) {
             $marca = Brands::inRandomOrder()->first() ?? Brands::factory()->create();
             $model = CarModels::factory()->create(['id_marca' => $marca->id]);
        } else {
             $marca = Brands::find($model->id_marca);
        }

        $marcha = Gears::inRandomOrder()->first() ?? Gears::factory()->create();
        $color = Color::inRandomOrder()->first() ?? Color::factory()->create();
        $combustible = Fuels::inRandomOrder()->first() ?? Fuels::factory()->create();

        // Asignar listing type aleatorio (1: Venta, 2: Alquiler)
        $listingType = ListingType::inRandomOrder()->first() ?? ListingType::factory()->create();

        $title = $marca->nombre . ' ' . $model->nombre;

        return [
            'title' => $title,
            "id_vendedor" => $vendedor->id,
            "id_marca" => $marca->id,
            "id_modelo" => $model->id,
            "id_marcha" => $marcha->id,
            "id_color" => $color->id,
            "id_combustible" => $combustible->id,
            "matricula" => $this->faker->numerify('####') . $this->faker->lexify('???'),
            "anyo_matri" => $this->faker->numberBetween(2000, 2025),
            "km" => $this->faker->numberBetween(100, 100000),
            "precio" => $this->faker->numberBetween(2000, 100000),
            "descripcion" => $this->faker->text(),
            "image" => 'cars/chat-im-cooked-v0-3z8fc1lv9khe1.webp',
            "id_estado" => $this->faker->numberBetween(1, 4),
            "id_listing_type" => $listingType->id, // Nuevo campo
        ];
    }
}
