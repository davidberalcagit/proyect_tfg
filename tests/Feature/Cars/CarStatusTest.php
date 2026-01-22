<?php

use App\Models\Cars;
use App\Models\CarStatus;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('cars can be created with different statuses', function () {
    $carForSale = Cars::factory()->create(['id_estado' => 1]);
    $carSold = Cars::factory()->create(['id_estado' => 2]);
    $carRented = Cars::factory()->create(['id_estado' => 4]);

    expect($carForSale->id_estado)->toBe(1);

    $statusName = CarStatus::find(1)->nombre;
    expect($carForSale->status->nombre)->toBe($statusName);

    expect($carSold->id_estado)->toBe(2);
    expect($carSold->status->nombre)->toBe('Vendido');
});

test('factory creates random statuses', function () {
    $cars = Cars::factory()->count(10)->create();

    foreach ($cars as $car) {
        expect($car->id_estado)->toBeIn([1, 2, 3, 4, 5, 6]);
        expect($car->status)->not->toBeNull();
    }
});
