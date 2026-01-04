<?php

namespace Database\Seeders;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Customers;
use App\Models\Dealerships;
use App\Models\EntityType;
use App\Models\Gears;
use App\Models\Individuals;
use App\Models\User;
use App\Models\Cars;
use App\Models\Sales;
use Database\Factories\IndividualsFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Pest\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * @param $cars
     */
    public function run(): void
    {
        $this->call(EntityTypesSeeder::class);
        $this->call(ColorsSeeder::class);
        $this->call(BrandsSeeder::class);
//        $this->call(CarModelsSeeder::class);
        $brands = Brands::all();
        foreach ($brands as $brand) {
            $cantidadModelos = rand(1, 3);
            for ($i = 0; $i < $cantidadModelos; $i++) {
                CarModels::create([
                    'id_marca' => $brand->id,
                    'nombre' => $brand->nombre . ' ' . Str::random(3),
                ]);
            }
        }


        $this->call(GearSeeder::class);
        $this->call(FuelsSeeder::class);
        $users = User::factory()->count(20)->create();
        $users->each(function ($user) {

            $customer = Customers::factory()->create([
                'id_usuario' => $user->id
            ]);

            $faker = fake();

            if ($customer->id_entidad == 1) {

                $customer->individuals()->create([
                    'id_cliente'      => $customer->id,
                    'dni'             => $faker->regexify('[0-9]{8}[A-Z]'),
                    'fecha_nacimiento'=> $faker->date(),
                ]);

            } else {

                $customer->dealerships()->create([
                    'id_cliente'     => $customer->id,
                    'nombre_empresa' => $faker->company(),
                    'nif'            => $faker->regexify('[A-Z][0-9]{8}'),
                    'direccion'      => $faker->address(),
                ]);
            }

        });
        $this->call(CarsSeeder::class);
        Sales::factory()->count(10)->create();

    }
}
