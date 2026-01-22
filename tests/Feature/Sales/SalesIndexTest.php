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
    // 1. Crear usuarios
    $sellerUser = User::factory()->create();
    $sellerUser->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    // 2. Crear datos

    // A. Oferta Pendiente (Debe salir en Received Offers del vendedor)
    $carOffer = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Oferta']);
    Offer::create([
        'id_vehiculo' => $carOffer->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'cantidad' => 10000,
        'estado' => 'pending'
    ]);

    // B. Venta Completada (Debe salir en My Sales del vendedor y My Purchases del comprador)
    $carSold = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Vendido']);
    Sales::create([
        'id_vehiculo' => $carSold->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'precio' => 20000,
        'id_estado' => 1
    ]);

    // C. Alquiler (Debe salir en My Leases del vendedor y My Rentals del comprador)
    $carRented = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Alquilado']);
    Rental::create([
        'id_vehiculo' => $carRented->id,
        'id_cliente' => $buyerCustomer->id,
        'fecha_inicio' => now(),
        'fecha_fin' => now()->addDays(5),
        'precio_total' => 500,
        'id_estado' => 1
    ]);

    // 3. Verificar vista del VENDEDOR
    $responseSeller = $this->actingAs($sellerUser)->get(route('sales.index'));
    $responseSeller->assertStatus(200);

    // Debe ver Oferta Pendiente
    $responseSeller->assertSee('Coche Oferta');
    // Debe ver Venta (My Sales)
    $responseSeller->assertSee('Coche Vendido');
    // Debe ver Arrendamiento (My Leases)
    $responseSeller->assertSee('Coche Alquilado');

    $purchases = $responseSeller->viewData('purchases');
    expect($purchases->isEmpty())->toBeTrue();

    $sales = $responseSeller->viewData('sales');
    expect($sales->count())->toBe(1);
    expect($sales->first()->vehiculo->title)->toBe('Coche Vendido');

    // 4. Verificar vista del COMPRADOR
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
