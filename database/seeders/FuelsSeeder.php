<?php

namespace Database\Seeders;

use App\Models\Fuels;
use App\Models\Gears;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fuels = ['Gasolina', 'Diesel', 'Electrico', 'Hibrido', 'Gas'];

        foreach ($fuels as $fuel) {
            Fuels::firstOrCreate(['nombre' => $fuel]);
        }
    }
}
