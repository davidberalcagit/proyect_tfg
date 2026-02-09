<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\ListingType;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('car created for rent is approved as for rent', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $rentTypeId = ListingType::where('nombre', 'Alquiler')->first()->id;

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'MarcaAlquiler',
        'temp_model' => 'ModeloAlquiler',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'RENT123',
        'anyo_matri' => 2024,
        'km' => 100,
        'precio' => 50,
        'descripcion' => 'Coche para alquilar',
        'id_listing_type' => $rentTypeId
    ]);

    $response->assertRedirect();

    $car = Cars::where('matricula', 'RENT123')->first();
    expect($car)->not->toBeNull();
    expect($car->id_estado)->toBe(4);
    expect($car->id_listing_type)->toBe($rentTypeId);

    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    $this->actingAs($supervisor)->post(route('supervisor.approve', $car->id));

    $car->refresh();
    expect($car->id_estado)->toBe(3);
});

test('car created for sale is approved as for sale', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $saleTypeId = ListingType::where('nombre', 'Venta')->first()->id;

    $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'MarcaVenta',
        'temp_model' => 'ModeloVenta',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'SALE123',
        'anyo_matri' => 2024,
        'km' => 100,
        'precio' => 20000,
        'descripcion' => 'Coche para vender',
        'id_listing_type' => $saleTypeId
    ]);

    $car = Cars::where('matricula', 'SALE123')->first();
    expect($car)->not->toBeNull();
    expect($car->id_estado)->toBe(4);
    expect($car->id_listing_type)->toBe($saleTypeId);

    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    $this->actingAs($supervisor)->post(route('supervisor.approve', $car->id));

    $car->refresh();
    expect($car->id_estado)->toBe(1);
});
