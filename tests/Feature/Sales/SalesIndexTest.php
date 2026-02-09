<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('sales index shows correct sections without duplication', function () {
    $sellerUser = User::factory()->create();
    $sellerUser->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    $carOffer = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Oferta']);
    Offer::create([
        'id_vehiculo' => $carOffer->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'cantidad' => 10000,
        'estado' => 'pending'
    ]);

    $carSold = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Vendido']);
    Sales::create([
        'id_vehiculo' => $carSold->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'precio' => 20000,
        'id_estado' => 1
    ]);

    $carRented = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Alquilado']);
    Rental::create([
        'id_vehiculo' => $carRented->id,
        'id_cliente' => $buyerCustomer->id,
        'fecha_inicio' => now(),
        'fecha_fin' => now()->addDays(5),
        'precio_total' => 500,
        'id_estado' => 1
    ]);

    $responseSeller = $this->actingAs($sellerUser)->get(route('sales.index'));
    $responseSeller->assertStatus(200);

    $responseSeller->assertSee('Coche Oferta');
    $responseSeller->assertSee('Coche Vendido');
    $responseSeller->assertSee('Coche Alquilado');

    $purchases = $responseSeller->viewData('purchases');
    expect($purchases->isEmpty())->toBeTrue();

    $sales = $responseSeller->viewData('sales');
    expect($sales->count())->toBe(1);
    expect($sales->first()->vehiculo->title)->toBe('Coche Vendido');

    $responseBuyer = $this->actingAs($buyerUser)->get(route('sales.index'));
    $responseBuyer->assertStatus(200);

    $receivedOffers = $responseBuyer->viewData('receivedOffers');
    expect($receivedOffers->isEmpty())->toBeTrue();

    $purchasesBuyer = $responseBuyer->viewData('purchases');
    expect($purchasesBuyer->count())->toBe(1);
    expect($purchasesBuyer->first()->vehiculo->title)->toBe('Coche Vendido');

    $rentalsBuyer = $responseBuyer->viewData('rentals');
    expect($rentalsBuyer->count())->toBe(1);
    expect($rentalsBuyer->first()->car->title)->toBe('Coche Alquilado');
});
