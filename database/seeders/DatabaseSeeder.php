<?php

namespace Database\Seeders;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Customers;
use App\Models\Dealerships;
use App\Models\EntityType;
use App\Models\Gears;
use App\Models\Individuals;
use App\Models\Offer;
use App\Models\User;
use App\Models\Cars;
use App\Models\Sales;
use Database\Factories\IndividualsFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        $this->call(RolesAndPermissionsSeeder::class);

        $this->call(EntityTypesSeeder::class);
        $this->call(ColorsSeeder::class);
        $this->call(BrandsSeeder::class);
        $this->call(CarModelsSeeder::class);
        $this->call(GearSeeder::class);
        $this->call(FuelsSeeder::class);
        $this->call(StatusesSeeder::class);
        $this->call(ListingTypesSeeder::class);
        $this->call(RentalStatusesSeeder::class);

        $testUser = User::where('email', 'a@gmail.com')->first();
        if (!$testUser) {
            $testUser = User::create([
                'name' => 'Test User',
                'email' => 'a@gmail.com',
                'password' => Hash::make('12345678'),
            ]);
            $testUser->assignRole('admin');

            $testCustomer = Customers::create([
                'id_usuario' => $testUser->id,
                'nombre_contacto' => 'Test User',
                'telefono' => '600000000',
                'id_entidad' => 1,
            ]);

            $testCustomer->individual()->create([
                'id_cliente' => $testCustomer->id,
                'dni' => '00000000T',
                'fecha_nacimiento' => '1990-01-01',
            ]);
        } else {
            $testCustomer = $testUser->customer;
            if(!$testUser->hasRole('admin')) {
                $testUser->syncRoles(['admin']);
            }
        }


        $users = User::factory()->count(20)->create();
        $users->each(function ($user) {
            $role = rand(0, 1) ? 'individual' : 'dealership';
            $user->assignRole($role);

            $faker = fake();
            $dealershipId = null;

            if ($role === 'dealership') {
                $dealership = Dealerships::create([
                    'nombre_empresa' => $faker->company(),
                    'nif'            => $faker->regexify('[A-Z][0-9]{8}'),
                    'direccion'      => $faker->address(),
                ]);
                $dealershipId = $dealership->id;
            }

            $customer = Customers::factory()->create([
                'id_usuario' => $user->id,
                'id_entidad' => $role === 'individual' ? 1 : 2,
                'dealership_id' => $dealershipId
            ]);

            if ($role === 'individual') {
                $customer->individual()->create([
                    'id_cliente'      => $customer->id,
                    'dni'             => $faker->regexify('[0-9]{8}[A-Z]'),
                    'fecha_nacimiento'=> $faker->date(),
                ]);
            }
        });

        $this->call(CarsSeeder::class);
        Sales::factory()->count(10)->create();
        $this->call(RentalsSeeder::class);

        $statuses = [
            1 => 'En Venta',
            2 => 'Vendido',
            3 => 'En Alquiler',
            4 => 'Pendiente de Revisión',
            5 => 'Rechazado',
            6 => 'Alquilado'
        ];

        foreach ($statuses as $id => $name) {
            $carData = [
                'id_vendedor' => $testCustomer->id,
                'id_estado' => $id,
                'title' => "Coche $name de Prueba",
                'id_listing_type' => ($id == 3 || $id == 6) ? 2 : 1,
            ];

            if ($id == 4 || $id == 5) {
                $carData['temp_brand'] = "Marca $name";
                $carData['temp_model'] = "Modelo $name";
                $carData['id_marca'] = null;
                $carData['id_modelo'] = null;
            }

            $car = Cars::factory()->create($carData);

            if ($id == 1) {
                Offer::factory()->count(2)->create([
                    'id_vehiculo' => $car->id,
                    'id_vendedor' => $testCustomer->id,
                ]);
            }
        }

        Offer::factory()->count(10)->create();

    }
}
