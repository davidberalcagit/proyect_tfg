<?php

namespace Database\Seeders;

use App\Models\Customers;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        foreach (User::all() as $user) {
            Customers::factory()->create([
                'id_usuario' => $user->id
            ]);
        }    }
}
