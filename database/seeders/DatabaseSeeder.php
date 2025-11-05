<?php

namespace Database\Seeders;

use App\Models\Compradores;
use App\Models\Empresas;
use App\Models\Particulares;
use App\Models\User;
use App\Models\Vehiculos;
use App\Models\Vendedores;
use App\Models\Ventas;
use Database\Factories\ParticularesFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {   //Vendedores
        $particularesV = Particulares::factory(3)->create();
        $empresasV = Empresas::factory(3)->create();

        $particularesV->each(fn($particularesV) =>
        Vendedores::create(['id_particular' => $particularesV->id])
        );

        $empresasV->each(fn($empresasV) =>
        Vendedores::create(['id_empresa' => $empresasV->id])
        );

        Vendedores::all()->each(fn($vendedores) =>
        Vehiculos::factory(1)->create(['id_vendedor' => $vendedores->id])
        );
        //Compradores
        $particularesC = Particulares::factory(3)->create();
        $empresasC = Empresas::factory(3)->create();

        $particularesC->each(fn($particularesC) =>
        Compradores::create(['id_particular' => $particularesC->id])
        );

        $empresasC->each(fn($empresasC) =>
        Compradores::create(['id_empresa' => $empresasC->id])
        );

        $compradores=Compradores::all();
        $vendedores=Vendedores::all();
        $vehiculos=Vehiculos::all();
        //Ventas
        $vendedores->each(function ($vendedores)use($vehiculos, $compradores){
            $randomComprador = $compradores->random();
            $randomVehiculos = $vehiculos->random();
            Ventas::create(['id_comprador' => $randomComprador->id,'id_vendedor' => $vendedores->id,'id_vehiculo' => $randomVehiculos->id ]);
        });
    }
}
