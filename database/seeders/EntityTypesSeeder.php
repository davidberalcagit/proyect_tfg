<?php

namespace Database\Seeders;

use App\Models\EntityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            EntityType::insert([
                ['nombre' => 'Individual'],
                ['nombre' => 'Dealership'],
            ]);
    }
}
