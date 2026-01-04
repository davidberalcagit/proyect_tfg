<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('colors')->insert([
            ['nombre' => 'Rojo'],
            ['nombre' => 'Azul'],
            ['nombre' => 'Verde'],
            ['nombre' => 'Negro'],
            ['nombre' => 'Blanco'],
            ['nombre' => 'Gris'],
            ['nombre' => 'Plata'],
            ['nombre' => 'Amarillo'],
        ]);
    }
}
