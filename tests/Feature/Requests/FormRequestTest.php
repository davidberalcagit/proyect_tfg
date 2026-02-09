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
        'precio' => 'not-a-number',
        'km' => 1000,
    ], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('precio');
    expect($validator->errors()->toArray())->toHaveKey('temp_brand');
});

test('store customer request validation', function () {
    $request = new StoreCustomerRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'telefono' => '',
        'id_entidad' => 99,
    ], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('telefono');
    expect($validator->errors()->toArray())->toHaveKey('id_entidad');
});
