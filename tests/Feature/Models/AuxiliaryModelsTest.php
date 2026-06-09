<?php

namespace Tests\Feature\Models;

use App\Models\CarStatus;
use App\Models\EntityType;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
use App\Models\RentalStatus;
use App\Models\SaleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('sale status can be created and updated', function () {
    $status = SaleStatus::create(['nombre' => 'Test Status ' . uniqid()]);
    $this->assertDatabaseHas('sale_statuses', ['nombre' => $status->nombre]);

    $newName = 'Updated Status ' . uniqid();
    $status->update(['nombre' => $newName]);
    $this->assertDatabaseHas('sale_statuses', ['nombre' => $newName]);
});

test('rental status can be created and updated', function () {
    $status = RentalStatus::create(['nombre' => 'Test Rental ' . uniqid()]);
    $this->assertDatabaseHas('rental_statuses', ['nombre' => $status->nombre]);

    $newName = 'Updated Rental ' . uniqid();
    $status->update(['nombre' => $newName]);
    $this->assertDatabaseHas('rental_statuses', ['nombre' => $newName]);
});

test('listing type can be created and updated', function () {
    $type = ListingType::create(['nombre' => 'Test Type ' . uniqid()]);
    $this->assertDatabaseHas('listing_types', ['nombre' => $type->nombre]);

    $newName = 'Updated Type ' . uniqid();
    $type->update(['nombre' => $newName]);
    $this->assertDatabaseHas('listing_types', ['nombre' => $newName]);
});


test('gears can be created, updated and deleted', function () {
    $gear = Gears::create(['tipo' => 'Test Gear ' . uniqid()]);
    $this->assertDatabaseHas('gears', ['tipo' => $gear->tipo]);

    $newName = 'Updated Gear ' . uniqid();
    $gear->update(['tipo' => $newName]);
    $this->assertDatabaseHas('gears', ['tipo' => $newName]);

    $gear->delete();
    $this->assertDatabaseMissing('gears', ['id' => $gear->id]);
});


test('fuels can be created, updated and deleted', function () {
    $fuel = Fuels::create(['nombre' => 'Test Fuel ' . uniqid()]);
    $this->assertDatabaseHas('fuels', ['nombre' => $fuel->nombre]);

    $newName = 'Updated Fuel ' . uniqid();
    $fuel->update(['nombre' => $newName]);
    $this->assertDatabaseHas('fuels', ['nombre' => $newName]);

    $fuel->delete();
    $this->assertDatabaseMissing('fuels', ['id' => $fuel->id]);
});


test('entity type can be created and updated', function () {
    $type = EntityType::create(['nombre' => 'Test Entity ' . uniqid()]);
    $this->assertDatabaseHas('entity_types', ['nombre' => $type->nombre]);

    $newName = 'Updated Entity ' . uniqid();
    $type->update(['nombre' => $newName]);
    $this->assertDatabaseHas('entity_types', ['nombre' => $newName]);
});


test('car status can be created and updated', function () {
    $status = CarStatus::create(['nombre' => 'Test Car Status ' . uniqid()]);
    $this->assertDatabaseHas('car_statuses', ['nombre' => $status->nombre]);

    $newName = 'Updated Car Status ' . uniqid();
    $status->update(['nombre' => $newName]);
    $this->assertDatabaseHas('car_statuses', ['nombre' => $newName]);
});
