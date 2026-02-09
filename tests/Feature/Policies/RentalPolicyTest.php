<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Rental;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('renter can view their rental', function () {
    $renter = User::factory()->create();
    $renter->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $renter->id]);

    $car = Cars::factory()->create();
    $rental = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $customer->id, 'fecha_inicio' => now(), 'fecha_fin' => now(), 'precio_total' => 100, 'id_estado' => 1]);

    expect($renter->can('view', $rental))->toBeTrue();
});

test('owner can view rental of their car', function () {
    $owner = User::factory()->create();
    $owner->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $owner->id]);

    $renter = Customers::factory()->create();

    $car = Cars::factory()->create(['id_vendedor' => $customer->id]);
    $rental = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $renter->id, 'fecha_inicio' => now(), 'fecha_fin' => now(), 'precio_total' => 100, 'id_estado' => 1]);

    expect($owner->can('view', $rental))->toBeTrue();
});

test('other user cannot view rental', function () {
    $other = User::factory()->create();
    $other->assignRole('individual');

    $renter = Customers::factory()->create();

    $car = Cars::factory()->create();
    $rental = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $renter->id, 'fecha_inicio' => now(), 'fecha_fin' => now(), 'precio_total' => 100, 'id_estado' => 1]);

    expect($other->can('view', $rental))->toBeFalse();
});
