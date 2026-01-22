<?php

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('car created with existing brand is pending approval', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $brand = Brands::first();
    $model = CarModels::where('id_marca', $brand->id)->first();

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'id_marca' => $brand->id,
        'id_modelo' => $model->id,
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => '1234ABC',
        'anyo_matri' => 2020,
        'km' => 10000,
        'precio' => 15000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => 1
    ]);

    $response->assertRedirect();

    $expectedTitle = "{$brand->nombre} {$model->nombre} 2020";

    $this->assertDatabaseHas('cars', [
        'title' => $expectedTitle,
        'id_estado' => 4
    ]);
});

test('car created with new brand is pending approval', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'MarcaFantasma',
        'temp_model' => 'ModeloX',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => '9999XYZ',
        'anyo_matri' => 2022,
        'km' => 5000,
        'precio' => 20000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => 1
    ]);

    $response->assertRedirect();

    $expectedTitle = "MarcaFantasma ModeloX 2022";

    $this->assertDatabaseHas('cars', [
        'title' => $expectedTitle,
        'id_estado' => 4,
        'temp_brand' => 'MarcaFantasma'
    ]);
});

test('supervisor can approve pending car and create brand', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $car = Cars::create([
        'title' => 'Coche Pendiente',
        'id_vendedor' => $customer->id,
        'temp_brand' => 'NuevaMarcaTest',
        'temp_model' => 'NuevoModeloTest',
        'id_estado' => 4,
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'TEST001',
        'anyo_matri' => 2023,
        'km' => 100,
        'precio' => 30000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => 1
    ]);

    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    $response = $this->actingAs($supervisor)->post(route('supervisor.approve', $car->id));

    $response->assertRedirect();

    $car->refresh();
    expect($car->id_estado)->toBe(1);
    expect($car->temp_brand)->toBeNull();

    $this->assertDatabaseHas('brands', ['nombre' => 'NuevaMarcaTest']);
    $this->assertDatabaseHas('car_models', ['nombre' => 'NuevoModeloTest']);

    expect($car->id_marca)->not->toBeNull();
    expect($car->id_modelo)->not->toBeNull();
});
