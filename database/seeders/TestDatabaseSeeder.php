<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            EntityTypesSeeder::class,
            StatusesSeeder::class,
            ListingTypesSeeder::class,
            RentalStatusesSeeder::class,
            ColorsSeeder::class,
            BrandsSeeder::class,
            CarModelsSeeder::class,
            GearSeeder::class,
            FuelsSeeder::class,
        ]);
    }
}
