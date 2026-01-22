<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Sales;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('buyer can view sale', function () {
    $buyer = User::factory()->create();
    $buyer->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $buyer->id]);

    // Crear vendedor real
    $seller = Customers::factory()->create();

    $car = Cars::factory()->create();
    $sale = Sales::create(['id_vehiculo' => $car->id, 'id_comprador' => $customer->id, 'id_vendedor' => $seller->id, 'precio' => 1000, 'id_estado' => 1]);

    expect($buyer->can('view', $sale))->toBeTrue();
});

test('seller can view sale', function () {
    $seller = User::factory()->create();
    $seller->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $seller->id]);

    // Crear comprador real
    $buyer = Customers::factory()->create();

    $car = Cars::factory()->create();
    $sale = Sales::create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $customer->id, 'precio' => 1000, 'id_estado' => 1]);

    expect($seller->can('view', $sale))->toBeTrue();
});
