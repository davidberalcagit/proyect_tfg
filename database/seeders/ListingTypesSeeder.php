<?php

namespace Database\Seeders;

use App\Models\ListingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListingTypesSeeder extends Seeder
{
    public function run(): void
    {
        // 1: Venta
        ListingType::firstOrCreate(['id' => 1], ['nombre' => 'Venta']);

        // 2: Alquiler
        ListingType::firstOrCreate(['id' => 2], ['nombre' => 'Alquiler']);
    }
}
