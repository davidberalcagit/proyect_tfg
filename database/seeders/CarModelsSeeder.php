<?php

namespace Database\Seeders;

use App\Models\CarModels;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CarModels::factory()->count(50)->create();
    }
}
