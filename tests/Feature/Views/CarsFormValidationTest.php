<?php

use App\Models\Brands;
use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('create car form displays validation errors', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $response = $this->actingAs($user)->post(route('cars.store'), []);

    $response->assertSessionHasErrors(['id_combustible', 'id_marcha', 'precio', 'anyo_matri', 'km', 'matricula']);
});

test('create car form displays duplicate error for temp brand', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $brand = Brands::first();

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => $brand->nombre,
        'temp_model' => 'ModeloCualquiera',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'DUP123',
        'anyo_matri' => 2023,
        'km' => 100,
        'precio' => 30000,
        'descripcion' => 'Test',
        'id_listing_type' => 1
    ]);

    $response->assertSessionHasErrors('temp_brand');
});
