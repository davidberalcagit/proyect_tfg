<?php

namespace Database\Seeders;

use App\Models\Gears;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gears::insert([
            ['tipo' => 'Manual'],
            ['tipo' => 'Automático'],
        ]);}
}
