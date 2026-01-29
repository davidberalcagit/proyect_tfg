<?php

use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\StoreCustomerRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

test('store car request validation', function () {
    $request = new StoreCarRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'precio' => 'not-a-number', // Invalid
        'km' => 1000,
        // Missing id_marca AND temp_brand
    ], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('precio');
    // id_marca is nullable, but temp_brand is required if id_marca is missing.
    expect($validator->errors()->toArray())->toHaveKey('temp_brand');
});

test('store customer request validation', function () {
    $request = new StoreCustomerRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'telefono' => '', // Required
        'id_entidad' => 99, // Invalid exists
    ], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('telefono');
    expect($validator->errors()->toArray())->toHaveKey('id_entidad');
});
