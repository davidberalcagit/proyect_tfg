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
    $model->nombre = 'Test';
    $model->save();
    expect($model->id)->not->toBeNull();
    expect(CarStatus::find($model->id)->nombre)->toBe('Test');
});

test('entity type coverage', function () {
    $model = new EntityType();
    $model->nombre = 'Test';
    $model->save();
    expect($model->id)->not->toBeNull();
});

test('fuels coverage', function () {
    $model = new Fuels();
    $model->nombre = 'Test';
    $model->save();
    expect($model->id)->not->toBeNull();
});

test('gears coverage', function () {
    $model = new Gears();
    $model->tipo = 'Test';
    $model->save();
    expect($model->id)->not->toBeNull();
});

test('listing type coverage', function () {
    $model = new ListingType();
    $model->nombre = 'Test';
    $model->save();
    expect($model->id)->not->toBeNull();
});

test('rental status coverage', function () {
    $model = new RentalStatus();
    $model->nombre = 'Test';
    $model->save();
    expect($model->id)->not->toBeNull();
});

test('sale status coverage', function () {
    $model = new SaleStatus();
    $model->nombre = 'Test';
    $model->save();
    expect($model->id)->not->toBeNull();
});
