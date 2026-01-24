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
    $car1 = Cars::factory()->create(['id_estado' => 1]); // En Venta
    $car2 = Cars::factory()->create(['id_estado' => 3]); // En Alquiler
    $car3 = Cars::factory()->create(['id_estado' => 2]); // Vendido

    $availableCars = Cars::available()->whereIn('id', [$car1->id, $car2->id, $car3->id])->get();

    expect($availableCars->count())->toBe(2);
    expect($availableCars->contains($car1))->toBeTrue();
    expect($availableCars->contains($car2))->toBeTrue();
    expect($availableCars->contains($car3))->toBeFalse();
});

test('cars by seller scope', function () {
    $seller1 = Customers::factory()->create();
    $seller2 = Customers::factory()->create();

    $car1 = Cars::factory()->create(['id_vendedor' => $seller1->id]);
    $car2 = Cars::factory()->create(['id_vendedor' => $seller2->id]);

    $cars1 = Cars::bySeller($seller1->id)->whereIn('id', [$car1->id, $car2->id])->get();

    expect($cars1->count())->toBe(1);
    expect($cars1->first()->id)->toBe($car1->id);
});

test('cars search scope', function () {
    $uniqueBrandName = 'MarcaUnicaTest' . rand(1000, 9999);
    $brand = Brands::factory()->create(['nombre' => $uniqueBrandName]);

    $car1 = Cars::factory()->create(['title' => 'Coche Rojo Rápido', 'descripcion' => 'Nada especial']);
    $car2 = Cars::factory()->create(['title' => 'Camión Azul', 'descripcion' => 'Es muy rápido']);
    $car3 = Cars::factory()->create(['title' => 'Otro Coche', 'id_marca' => $brand->id]);

    // Filtramos por IDs para evitar falsos positivos del seeder
    $ids = [$car1->id, $car2->id, $car3->id];

    $results = Cars::search('Rojo')->whereIn('id', $ids)->get();
    expect($results->contains($car1))->toBeTrue();
    expect($results->contains($car2))->toBeFalse();

    $results = Cars::search('rápido')->whereIn('id', $ids)->get();
    expect($results->contains($car1))->toBeTrue();
    expect($results->contains($car2))->toBeTrue();

    $results = Cars::search($uniqueBrandName)->whereIn('id', $ids)->get();
    expect($results->contains($car3))->toBeTrue();
});

test('cars recent scope', function () {
    $recent = Cars::factory()->create(['created_at' => now()]);
    $old = Cars::factory()->create(['created_at' => now()->subDays(10)]);

    $recentCars = Cars::recent(7)->whereIn('id', [$recent->id, $old->id])->get();

    expect($recentCars->count())->toBe(1);
    expect($recentCars->first()->id)->toBe($recent->id);
});

test('cars cheap scope', function () {
    $cheap = Cars::factory()->create(['precio' => 4000]);
    $expensive = Cars::factory()->create(['precio' => 6000]);

    $cheapCars = Cars::cheap(5000)->whereIn('id', [$cheap->id, $expensive->id])->get();

    expect($cheapCars->count())->toBe(1);
    expect($cheapCars->first()->id)->toBe($cheap->id);
});

test('offer pending scope', function () {
    $car = Cars::first() ?? Cars::factory()->create();
    $buyer = Customers::first() ?? Customers::factory()->create();
    $seller = Customers::factory()->create();

    $pending = Offer::create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id, 'cantidad' => 100, 'estado' => 'pending']);
    $accepted = Offer::create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id, 'cantidad' => 100, 'estado' => 'accepted']);

    $pendingOffers = Offer::pending()->whereIn('id', [$pending->id, $accepted->id])->get();

    expect($pendingOffers->count())->toBe(1);
    expect($pendingOffers->first()->id)->toBe($pending->id);
});

test('offer for seller scope', function () {
    $seller = Customers::factory()->create();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);
    $buyer = Customers::factory()->create();

    $offer = Offer::create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id, 'cantidad' => 100, 'estado' => 'pending']);

    $offers = Offer::forSeller($seller->id)->where('id', $offer->id)->get();

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

test('rental active scope', function () {
    $car = Cars::factory()->create(['id_estado' => 3]);
    $customer = Customers::factory()->create();

    $active = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $customer->id, 'fecha_inicio' => now(), 'fecha_fin' => now(), 'precio_total' => 100, 'id_estado' => 3]); // Activo
    $pending = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $customer->id, 'fecha_inicio' => now(), 'fecha_fin' => now(), 'precio_total' => 100, 'id_estado' => 2]); // Pendiente

    $activeRentals = Rental::active()->whereIn('id', [$active->id, $pending->id])->get();

    expect($activeRentals->count())->toBe(1);
    expect($activeRentals->first()->id)->toBe($active->id);
});
