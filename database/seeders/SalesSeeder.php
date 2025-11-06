<?php

namespace Database\Seeders;

use App\Models\Vehiculos;
use App\Models\Ventas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VentasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ventas::factory()->count(5)->create();
    }
}
