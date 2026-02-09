<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
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
});

test('cannot edit approved car unless admin', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 1
    ]);

    $response = $this->actingAs($user)->get(route('cars.edit', $car));
    $response->assertStatus(403);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('cars.edit', $car));
    $response->assertStatus(200);
});
