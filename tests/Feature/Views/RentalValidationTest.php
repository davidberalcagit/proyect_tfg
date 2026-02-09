<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('rental end date must be after or equal start date', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create(['id_estado' => 3]);

    $today = now()->format('Y-m-d');
    $yesterday = now()->subDay()->format('Y-m-d');

    $response = $this->actingAs($user)->post(route('rentals.store', $car), [
        'fecha_inicio' => $today,
        'fecha_fin' => $yesterday,
    ]);

    $response->assertSessionHasErrors(['fecha_fin']);

    $responseSuccess = $this->actingAs($user)->post(route('rentals.store', $car), [
        'fecha_inicio' => $today,
        'fecha_fin' => $today,
    ]);

    $responseSuccess->assertSessionHasNoErrors();
});

test('rental start date cannot be in past', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create(['id_estado' => 3]);

    $pastDate = now()->subDays(5)->format('Y-m-d');

    $response = $this->actingAs($user)->post(route('rentals.store', $car), [
        'fecha_inicio' => $pastDate,
        'fecha_fin' => now()->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['fecha_inicio']);
});
