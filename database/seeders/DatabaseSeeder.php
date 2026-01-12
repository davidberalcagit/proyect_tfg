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
        // Roles and Permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        $this->call(EntityTypesSeeder::class);
        $this->call(ColorsSeeder::class);
        $this->call(BrandsSeeder::class);
        $this->call(CarModelsSeeder::class);

        $this->call(GearSeeder::class);
        $this->call(FuelsSeeder::class);
        $this->call(StatusesSeeder::class);

        // Create specific user for testing only if it doesn't exist
        $testUser = User::where('email', 'a@gmail.com')->first();
        if (!$testUser) {
            $testUser = User::create([
                'name' => 'Test User',
                'email' => 'a@gmail.com',
                'password' => Hash::make('12345678'),
            ]);
            $testUser->assignRole('admin'); // Changed to admin

            $testCustomer = Customers::create([
                'id_usuario' => $testUser->id,
                'nombre_contacto' => 'Test User',
                'telefono' => '600000000',
                'id_entidad' => 1, // Individual
            ]);

            $testCustomer->individual()->create([
                'id_cliente' => $testCustomer->id,
                'dni' => '00000000T',
                'fecha_nacimiento' => '1990-01-01',
            ]);
        } else {
            $testCustomer = $testUser->customer;
            // Ensure role is admin if user already exists
            if(!$testUser->hasRole('admin')) {
                $testUser->syncRoles(['admin']);
            }
        }


        $users = User::factory()->count(20)->create();
        $users->each(function ($user) {
            // Assign random role for seeded users
            // 50% individual, 50% dealership
            $role = rand(0, 1) ? 'individual' : 'dealership';
            $user->assignRole($role);

            $customer = Customers::factory()->create([
                'id_usuario' => $user->id,
                'id_entidad' => $role === 'individual' ? 1 : 2
            ]);

            $faker = fake();

            if ($customer->id_entidad == 1) {

                $customer->individual()->create([
                    'id_cliente'      => $customer->id,
                    'dni'             => $faker->regexify('[0-9]{8}[A-Z]'),
                    'fecha_nacimiento'=> $faker->date(),
                ]);

            } else {

                $customer->dealership()->create([
                    'id_cliente'     => $customer->id,
                    'nombre_empresa' => $faker->company(),
                    'nif'            => $faker->regexify('[A-Z][0-9]{8}'),
                    'direccion'      => $faker->address(),
                ]);
            }

        });
        $this->call(CarsSeeder::class);
        Sales::factory()->count(10)->create();

        // Create cars for the test user so they can receive offers
        $testUserCars = Cars::factory()->count(3)->create([
            'id_vendedor' => $testCustomer->id,
            'id_estado' => 1 // En venta
        ]);

        // Create offers for these cars
        foreach ($testUserCars as $car) {
            Offer::factory()->count(2)->create([
                'id_vehiculo' => $car->id,
                'id_vendedor' => $testCustomer->id,
                // id_comprador will be random from factory
            ]);
        }

        // Create some random offers
        Offer::factory()->count(10)->create();

    }
}
