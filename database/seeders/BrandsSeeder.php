<?php

namespace Database\Seeders;

use App\Models\Brands;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Toyota','BMW','Mercedes','Renault','Volvo',
            'Ford','Hyundai','Volkswagen','Tesla','Audi'
        ];

        foreach ($brands as $brand) {
            Brands::create(['nombre' => $brand]);
        }
    }
}
