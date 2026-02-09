<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('admin can edit approved car', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Customers::factory()->create(['id_usuario' => $admin->id]);

    $car = Cars::factory()->create([
        'id_estado' => 1
    ]);

    $response = $this->actingAs($admin)->get(route('cars.edit', $car));
    $response->assertStatus(200);

    $response = $this->actingAs($admin)->put(route('cars.update', $car), [
        'precio' => 99999,
        'id_marca' => $car->id_marca,
        'id_modelo' => $car->id_modelo,
        'id_marcha' => $car->id_marcha,
        'id_combustible' => $car->id_combustible,
        'id_color' => $car->id_color,
        'matricula' => $car->matricula,
        'anyo_matri' => $car->anyo_matri,
        'km' => $car->km,
        'descripcion' => 'Admin edit',
        'id_listing_type' => 1
    ]);

    $response->assertRedirect(route('cars.index'));
    $this->assertDatabaseHas('cars', ['id' => $car->id, 'precio' => 99999]);
});

test('owner can edit pending car', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 4
    ]);

    $response = $this->actingAs($user)->get(route('cars.edit', $car));
    $response->assertStatus(200);

    $response = $this->actingAs($user)->put(route('cars.update', $car), [
        'precio' => 5000,
        'id_marca' => $car->id_marca,
        'id_modelo' => $car->id_modelo,
        'id_marcha' => $car->id_marcha,
        'id_combustible' => $car->id_combustible,
        'id_color' => $car->id_color,
        'matricula' => $car->matricula,
        'anyo_matri' => $car->anyo_matri,
        'km' => $car->km,
        'descripcion' => 'Owner edit',
        'id_listing_type' => 1
    ]);

    $response->assertRedirect(route('cars.index'));
});

test('owner cannot edit approved car', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 1
    ]);

    $response = $this->actingAs($user)->get(route('cars.edit', $car));
    $response->assertStatus(403);

    $response = $this->actingAs($user)->put(route('cars.update', $car), [
        'precio' => 5000
    ]);
    $response->assertStatus(403);
});

test('user cannot edit others car', function () {
    $owner = User::factory()->create();
    $owner->assignRole('individual');
    $ownerCustomer = Customers::factory()->create(['id_usuario' => $owner->id]);

    $otherUser = User::factory()->create();
    $otherUser->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $otherUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $ownerCustomer->id,
        'id_estado' => 4
    ]);

    $response = $this->actingAs($otherUser)->get(route('cars.edit', $car));
    $response->assertStatus(403);

    $response = $this->actingAs($otherUser)->put(route('cars.update', $car), [
        'precio' => 5000
    ]);
    $response->assertStatus(403);
});
