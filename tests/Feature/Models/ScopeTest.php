<?php

use App\Models\Brands;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\User;
use App\Models\Sales;
use App\Models\EntityType;

beforeEach(function () {
    $this->seed(Database\Seeders\TestDatabaseSeeder::class);
});

test('cars available scope', function () {
    $car1 = Cars::factory()->create(['id_estado' => 1]);
    $car2 = Cars::factory()->create(['id_estado' => 3]);
    $car3 = Cars::factory()->create(['id_estado' => 2]);

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

test('offer pending scope', function () {
    $car = Cars::factory()->create();
    $buyer = Customers::factory()->create();
    $seller = Customers::factory()->create();

    $pending = Offer::factory()->create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id, 'estado' => 'pending']);
    $accepted = Offer::factory()->create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id, 'estado' => 'accepted']);

    $pendingOffers = Offer::pending()->whereIn('id', [$pending->id, $accepted->id])->get();

    expect($pendingOffers->count())->toBe(1);
    expect($pendingOffers->first()->id)->toBe($pending->id);
});

test('offer for seller scope', function () {
    $seller = Customers::factory()->create();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);
    $buyer = Customers::factory()->create();

    $offer = Offer::factory()->create(['id_vehiculo' => $car->id, 'id_comprador' => $buyer->id, 'id_vendedor' => $seller->id]);

    $offers = Offer::forSeller($seller->id)->where('id', $offer->id)->get();

    expect($offers)->not->toBeEmpty();
    expect($offers->first()->id_vendedor)->toBe($seller->id);
});

test('rental overlapping scope', function () {
    $car = Cars::factory()->create(['id_estado' => 3]);
    $customer = Customers::factory()->create();

    Rental::factory()->create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => '2024-01-10',
        'fecha_fin' => '2024-01-15',
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

    $active = Rental::factory()->create(['id_vehiculo' => $car->id, 'id_cliente' => $customer->id, 'id_estado' => 3]);
    $pending = Rental::factory()->create(['id_vehiculo' => $car->id, 'id_cliente' => $customer->id, 'id_estado' => 2]);

    $activeRentals = Rental::active()->whereIn('id', [$active->id, $pending->id])->get();

    expect($activeRentals->count())->toBe(1);
    expect($activeRentals->first()->id)->toBe($active->id);
});

test('user active traders scope', function () {
    $user1 = User::factory()->create();
    $customer1 = Customers::factory()->create(['id_usuario' => $user1->id]);
    Cars::factory()->create(['id_vendedor' => $customer1->id, 'created_at' => now()]);

    $user2 = User::factory()->create();
    $customer2 = Customers::factory()->create(['id_usuario' => $user2->id]);
    $car2 = Cars::factory()->create(['id_vendedor' => $customer2->id, 'created_at' => now()->subYear()]);
    $buyer = Customers::factory()->create();
    Sales::factory()->create(['id_vehiculo' => $car2->id, 'id_vendedor' => $customer2->id, 'id_comprador' => $buyer->id, 'created_at' => now()->subDays(15)]);

    $user3 = User::factory()->create();
    $customer3 = Customers::factory()->create(['id_usuario' => $user3->id]);
    Cars::factory()->create(['id_vendedor' => $customer3->id, 'created_at' => now()->subYear()]);

    $activeTraders = User::activeTraders()->whereIn('id', [$user1->id, $user2->id, $user3->id])->get();

    expect($activeTraders->count())->toBe(2);
    expect($activeTraders->contains($user1))->toBeTrue();
    expect($activeTraders->contains($user2))->toBeTrue();
    expect($activeTraders->contains($user3))->toBeFalse();
});


test('sales getReportData works correctly', function () {
    $brand1 = Brands::factory()->create();
    $brand2 = Brands::factory()->create();

    $entityType1 = EntityType::firstOrCreate(['id' => 1, 'nombre' => 'individual']);
    $entityType2 = EntityType::firstOrCreate(['id' => 2, 'nombre' => 'dealership']);

    $seller1 = Customers::factory()->create(['id_entidad' => $entityType1->id]);
    $seller2 = Customers::factory()->create(['id_entidad' => $entityType2->id]);

    $car1 = Cars::factory()->create(['id_marca' => $brand1->id]);
    $car2 = Cars::factory()->create(['id_marca' => $brand2->id]);

    Sales::factory()->count(2)->create(['id_vendedor' => $seller1->id, 'id_vehiculo' => $car1->id, 'id_estado' => 1]);
    Sales::factory()->create(['id_vendedor' => $seller2->id, 'id_vehiculo' => $car2->id, 'id_estado' => 1]);

    $summary = Sales::getReportData();

    expect($summary['top_sellers'])->toHaveCount(2);
    expect($summary['top_sellers']->first()->id)->toBe($seller1->id);
    expect($summary['top_sellers']->first()->total_sales)->toBe(2);

    $salesByType = collect($summary['sales_by_type'])->keyBy('nombre');
    expect($salesByType['individual']->total)->toBe(2);
    expect($salesByType['dealership']->total)->toBe(1);

    expect($summary['popular_brand'])->toContain($brand1->nombre);
});

test('top sellers scope only includes valid sales', function () {
    $seller = Customers::factory()->create();

    Sales::factory()->create(['id_vendedor' => $seller->id, 'id_estado' => 1]);
    Sales::factory()->create(['id_vendedor' => $seller->id, 'id_estado' => 2]);

    $topSellers = Customers::topSellers()->get();

    expect($topSellers)->toHaveCount(1);
    expect($topSellers->first()->total_sales)->toBe(1);
});
