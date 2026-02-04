<?php

use App\Models\CarStatus;
use App\Models\EntityType;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
use App\Models\RentalStatus;
use App\Models\SaleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('car status coverage', function () {
    $model = new CarStatus();
    $model->fill(['nombre' => 'Test']);
    $model->save();
    expect($model->getLabel())->toBe('Test');
});

test('entity type coverage', function () {
    $model = new EntityType();
    $model->fill(['nombre' => 'Test']);
    $model->save();
    expect($model->getLabel())->toBe('Test');
});

test('fuels coverage', function () {
    $model = new Fuels();
    $model->fill(['nombre' => 'Test']);
    $model->save();
    expect($model->getLabel())->toBe('Test');
});

test('gears coverage', function () {
    $model = new Gears();
    $model->fill(['tipo' => 'Test']);
    $model->save();
    expect($model->getLabel())->toBe('Test');
});

test('listing type coverage', function () {
    $model = new ListingType();
    $model->fill(['nombre' => 'Test']);
    $model->save();
    expect($model->getLabel())->toBe('Test');
});

test('rental status coverage', function () {
    $model = new RentalStatus();
    $model->fill(['nombre' => 'Test']);
    $model->save();
    expect($model->getLabel())->toBe('Test');
});

test('sale status coverage', function () {
    $model = new SaleStatus();
    $model->fill(['nombre' => 'Test']);
    $model->save();
    expect($model->getLabel())->toBe('Test');
});
