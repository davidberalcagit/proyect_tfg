<?php

namespace Database\Seeders;

use App\Models\Rental;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentalsSeeder extends Seeder
{
    public function run(): void
    {
        Rental::factory()->count(20)->create();
    }
}
