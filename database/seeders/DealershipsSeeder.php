<?php

namespace Database\Seeders;

use App\Models\Dealerships;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealershipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dealerships::factory()->count(1)->create();

    }
}
