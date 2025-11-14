<?php

namespace Database\Seeders;

use App\Models\Buyers;
use App\Models\Dealerships;
use App\Models\Individuals;
use App\Models\User;
use App\Models\Cars;
use App\Models\Sellers;
use App\Models\Sales;
use Database\Factories\IndividualsFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * @param $cars
     */
    public function run(): void
    {   //Sellers
        $particularesV = Individuals::factory(3)->create();
        $empresasV = Dealerships::factory(3)->create();

        $particularesV->each(fn($particularesV) =>
        Sellers::create(['id_particular' => $particularesV->id])
        );

        $empresasV->each(fn($empresasV) =>
        Sellers::create(['id_empresa' => $empresasV->id])
        );

        Sellers::all()->each(fn($sellers) =>
        Cars::factory(1)->create(['id_vendedor' => $sellers->id])
        );
        //Buyers
        $particularesC = Individuals::factory(3)->create();
        $empresasC = Dealerships::factory(3)->create();

        $particularesC->each(fn($particularesC) =>
        Buyers::create(['id_particular' => $particularesC->id])
        );

        $empresasC->each(fn($empresasC) =>
        Buyers::create(['id_empresa' => $empresasC->id])
        );

        $buyers=Buyers::all();
        $sellers=Sellers::all();
        $cars=Cars::all();
        //Sales
        $sellers->each(function ($sellers)use($cars, $buyers){
            $randomBuyers = $buyers->random();
            $randomCars = $cars->random();
            Sales::create(['id_comprador' => $randomBuyers->id,'id_vendedor' => $sellers->id,'id_vehiculo' => $randomCars->id ]);
        });
    }
}
