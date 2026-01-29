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

// --- SaleStatus ---
test('sale status can be created and updated', function () {
    $status = SaleStatus::create(['id' => 1, 'nombre' => 'Pendiente']);
    $this->assertDatabaseHas('sale_statuses', ['nombre' => 'Pendiente']);

    $status->update(['nombre' => 'Vendido']);
    $this->assertDatabaseHas('sale_statuses', ['nombre' => 'Vendido']);
});

// --- RentalStatus ---
test('rental status can be created and updated', function () {
    $status = RentalStatus::create(['nombre' => 'Activo']);
    $this->assertDatabaseHas('rental_statuses', ['nombre' => 'Activo']);

    $status->update(['nombre' => 'Finalizado']);
    $this->assertDatabaseHas('rental_statuses', ['nombre' => 'Finalizado']);
});

// --- ListingType ---
test('listing type can be created and updated', function () {
    $type = ListingType::create(['nombre' => 'Venta']);
    $this->assertDatabaseHas('listing_types', ['nombre' => 'Venta']);

    $type->update(['nombre' => 'Alquiler']);
    $this->assertDatabaseHas('listing_types', ['nombre' => 'Alquiler']);
});

// --- Gears ---
test('gears can be created, updated and deleted', function () {
    $gear = Gears::create(['tipo' => 'Manual']);
    $this->assertDatabaseHas('gears', ['tipo' => 'Manual']);

    $gear->update(['tipo' => 'Automatic']);
    $this->assertDatabaseHas('gears', ['tipo' => 'Automatic']);

    $gear->delete();
    $this->assertDatabaseMissing('gears', ['id' => $gear->id]);
});

// --- Fuels ---
test('fuels can be created, updated and deleted', function () {
    $fuel = Fuels::create(['nombre' => 'Gasolina']);
    $this->assertDatabaseHas('fuels', ['nombre' => 'Gasolina']);

    $fuel->update(['nombre' => 'Diesel']);
    $this->assertDatabaseHas('fuels', ['nombre' => 'Diesel']);

    $fuel->delete();
    $this->assertDatabaseMissing('fuels', ['id' => $fuel->id]);
});

// --- EntityType ---
test('entity type can be created and updated', function () {
    $type = EntityType::create(['nombre' => 'Particular']);
    $this->assertDatabaseHas('entity_types', ['nombre' => 'Particular']);

    $type->update(['nombre' => 'Empresa']);
    $this->assertDatabaseHas('entity_types', ['nombre' => 'Empresa']);
});

// --- CarStatus ---
test('car status can be created and updated', function () {
    $status = CarStatus::create(['id' => 1, 'nombre' => 'En Venta']);
    $this->assertDatabaseHas('car_statuses', ['nombre' => 'En Venta']);

    $status->update(['nombre' => 'Vendido']);
    $this->assertDatabaseHas('car_statuses', ['nombre' => 'Vendido']);
});
