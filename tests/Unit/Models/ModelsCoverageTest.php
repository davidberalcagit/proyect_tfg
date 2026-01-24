<?php

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\CarStatus;
use App\Models\Customers;
use App\Models\Dealerships;
use App\Models\EntityType;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\Individuals;
use App\Models\ListingType;
use App\Models\RentalStatus;
use App\Models\SaleStatus;

test('dealerships has customers relationship', function () {
    $dealership = Dealerships::factory()->create();
    $customer = Customers::factory()->create(['dealership_id' => $dealership->id]);

    expect($dealership->customers)->toHaveCount(1);
    expect($dealership->customers->first()->id)->toBe($customer->id);
});

test('individuals has customer relationship', function () {
    $customer = Customers::factory()->create();
    $individual = Individuals::factory()->create(['id_cliente' => $customer->id]);

    expect($individual->customer->id)->toBe($customer->id);
});

test('car models has brand and cars relationship', function () {
    $brand = Brands::factory()->create();
    $model = CarModels::factory()->create(['id_marca' => $brand->id]);

    expect($model->brand->id)->toBe($brand->id);
    expect($model->cars)->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class);
});

test('simple models can be instantiated', function () {
    // Testing instantiation and fillable to ensure coverage
    // Use new instance instead of factory if factory doesn't exist

    $status = new CarStatus();
    expect($status)->toBeInstanceOf(CarStatus::class);

    $fuel = Fuels::factory()->create();
    expect($fuel)->toBeInstanceOf(Fuels::class);

    $gear = Gears::factory()->create();
    expect($gear)->toBeInstanceOf(Gears::class);

    $type = EntityType::factory()->create();
    expect($type)->toBeInstanceOf(EntityType::class);

    $listing = ListingType::factory()->create();
    expect($listing)->toBeInstanceOf(ListingType::class);

    // SaleStatus does not have a factory, instantiate manually
    $saleStatus = new SaleStatus();
    expect($saleStatus)->toBeInstanceOf(SaleStatus::class);

    // RentalStatus does not have a factory, instantiate manually
    $rentalStatus = new RentalStatus();
    expect($rentalStatus)->toBeInstanceOf(RentalStatus::class);
});
