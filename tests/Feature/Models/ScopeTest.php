<?php

use App\Models\Brands;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('cars available scope', function () {
    Cars::factory()->create(['id_estado' => 1]); // En Venta
    Cars::factory()->create(['id_estado' => 3]); // En Alquiler
    Cars::factory()->create(['id_estado' => 2]); // Vendido
    Cars::factory()->create(['id_estado' => 4]); // Pendiente

    $availableCars = Cars::available()->get();

    foreach ($availableCars as $car) {
        expect($car->id_estado)->toBeIn([1, 3]);
    }
});

test('cars by seller scope', function () {
    $seller1 = Customers::factory()->create();
    $seller2 = Customers::factory()->create();

    Cars::factory()->create(['id_vendedor' => $seller1->id]);
    Cars::factory()->create(['id_vendedor' => $seller2->id]);

    $cars1 = Cars::bySeller($seller1->id)->get();

    foreach ($cars1 as $car) {
        expect($car->id_vendedor)->toBe($seller1->id);
    }
});

test('cars search scope', function () {
    $uniqueBrandName = 'MarcaUnicaTest' . rand(1000, 9999);
    $brand = Brands::factory()->create(['nombre' => $uniqueBrandName]);

    $car1 = Cars::factory()->create(['title' => 'Coche Rojo Rápido', 'descripcion' => 'Nada especial']);
    $car2 = Cars::factory()->create(['title' => 'Camión Azul', 'descripcion' => 'Es muy rápido']);
    $car3 = Cars::factory()->create(['title' => 'Otro Coche', 'id_marca' => $brand->id]);

    $results = Cars::search('Rojo')->get();
    expect($results->contains($car1))->toBeTrue();
    expect($results->contains($car2))->toBeFalse();

    $results = Cars::search('rápido')->get();
    expect($results->contains($car1))->toBeTrue();
    expect($results->contains($car2))->toBeTrue();

    $results = Cars::search($uniqueBrandName)->get();
    expect($results->contains($car3))->toBeTrue();
});

test('offer pending scope', function () {
    $car = Cars::first() ?? Cars::factory()->create();
    $buyer = Customers::first() ?? Customers::factory()->create();
    $seller = Customers::factory()->create();

    Offer::create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id, 'cantidad' => 100, 'estado' => 'pending']);
    Offer::create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id, 'cantidad' => 100, 'estado' => 'accepted']);

    $pendingOffers = Offer::pending()->get();

    foreach ($pendingOffers as $offer) {
        expect($offer->estado)->toBe('pending');
    }
});

test('offer for seller scope', function () {
    $seller = Customers::factory()->create();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);
    $buyer = Customers::factory()->create();

    Offer::create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id, 'cantidad' => 100, 'estado' => 'pending']);

    $offers = Offer::forSeller($seller->id)->get();

    expect($offers)->not->toBeEmpty();
    expect($offers->first()->id_vendedor)->toBe($seller->id);
});

test('rental overlapping scope', function () {
    $car = Cars::factory()->create(['id_estado' => 3]);
    $customer = Customers::first() ?? Customers::factory()->create();

    Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => '2024-01-10',
        'fecha_fin' => '2024-01-15',
        'precio_total' => 100,
        'id_estado' => 2
    ]);

    expect(Rental::overlapping($car->id, '2024-01-12', '2024-01-13')->exists())->toBeTrue();
    expect(Rental::overlapping($car->id, '2024-01-08', '2024-01-12')->exists())->toBeTrue();
    expect(Rental::overlapping($car->id, '2024-01-14', '2024-01-18')->exists())->toBeTrue();
    expect(Rental::overlapping($car->id, '2024-01-05', '2024-01-20')->exists())->toBeTrue();
    expect(Rental::overlapping($car->id, '2024-01-01', '2024-01-05')->exists())->toBeFalse();
    expect(Rental::overlapping($car->id, '2024-01-20', '2024-01-25')->exists())->toBeFalse();
});
