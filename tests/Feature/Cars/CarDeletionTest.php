<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('cannot delete car with completed sale', function () {
    $seller = User::factory()->create();
    $seller->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

    $buyerCustomer = Customers::factory()->create();

    $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

    Sales::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'precio' => 10000,
        'id_estado' => 1
    ]);

    $response = $this->actingAs($seller)->delete(route('cars.destroy', $car));

    $response->assertRedirect()->assertSessionHas('error');
    $this->assertDatabaseHas('cars', ['id' => $car->id]);
});

test('cannot delete car with rental', function () {
    $seller = User::factory()->create();
    $seller->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

    $renterCustomer = Customers::factory()->create();

    $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

    Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $renterCustomer->id,
        'fecha_inicio' => now(),
        'fecha_fin' => now()->addDays(1),
        'precio_total' => 100,
        'id_estado' => 1
    ]);

    $response = $this->actingAs($seller)->delete(route('cars.destroy', $car));

    $response->assertRedirect()->assertSessionHas('error');
    $this->assertDatabaseHas('cars', ['id' => $car->id]);
});

test('cannot delete car with accepted offer', function () {
    $seller = User::factory()->create();
    $seller->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

    $buyerCustomer = Customers::factory()->create();

    $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

    Offer::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'cantidad' => 10000,
        'estado' => 'accepted_by_seller'
    ]);

    $response = $this->actingAs($seller)->delete(route('cars.destroy', $car));

    $response->assertRedirect()->assertSessionHas('error');
    $this->assertDatabaseHas('cars', ['id' => $car->id]);
});

test('can delete car without transactions', function () {
    $seller = User::factory()->create();
    $seller->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

    $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

    $response = $this->actingAs($seller)->delete(route('cars.destroy', $car));

    $response->assertRedirect(route('cars.index'));
    $this->assertDatabaseMissing('cars', ['id' => $car->id]);
});
