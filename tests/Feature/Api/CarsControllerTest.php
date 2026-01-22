<?php

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('api can list cars', function () {
    Cars::factory()->count(3)->create(['id_estado' => 1]);

    $response = $this->getJson('/api/cars');

    $response->assertStatus(200)
             ->assertJsonStructure(['data', 'links']);
});

test('api can show car', function () {
    $car = Cars::factory()->create(['id_estado' => 1]);

    $response = $this->getJson("/api/cars/{$car->id}");

    $response->assertStatus(200)
             ->assertJson(['id' => $car->id]);
});

test('api can create car', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $brand = Brands::first();
    $model = CarModels::where('id_marca', $brand->id)->first();

    $response = $this->actingAs($user)->postJson('/api/cars', [
        'id_marca' => $brand->id,
        'id_modelo' => $model->id,
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'API1234',
        'anyo_matri' => 2024,
        'km' => 100,
        'precio' => 20000,
        'descripcion' => 'API Test Car',
        'id_listing_type' => 1
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('cars', ['matricula' => 'API1234']);
});

test('api cannot create car without auth', function () {
    $response = $this->postJson('/api/cars', []);
    $response->assertStatus(401);
});
