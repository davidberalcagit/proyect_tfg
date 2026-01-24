<?php

use App\Http\Requests\StoreCarRequest;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Mockery\MockInterface;

test('request authorizes user with permission', function () {
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('can')
         ->with('create', Cars::class)
         ->andReturn(true);

    $request = new StoreCarRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

test('request denies user without permission', function () {
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('can')
         ->with('create', Cars::class)
         ->andReturn(false);

    $request = new StoreCarRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeFalse();
});

test('validation fails for duplicate temp brand', function () {
    $user = User::factory()->create();

    // Create required dependencies for validation
    $fuel = Fuels::factory()->create();
    $gear = Gears::factory()->create();
    $listingType = ListingType::factory()->create();
    $color = Color::factory()->create();

    Brands::factory()->create(['nombre' => 'ExistingBrand']);

    $data = [
        'temp_brand' => 'ExistingBrand',
        'id_marca' => null,
        'temp_model' => 'NewModel',
        'id_combustible' => $fuel->id,
        'id_marcha' => $gear->id,
        'precio' => 1000,
        'anyo_matri' => 2020,
        'km' => 1000,
        'matricula' => 'ABC',
        'id_color' => $color->id,
        'descripcion' => 'desc',
        'id_listing_type' => $listingType->id
    ];

    $request = new StoreCarRequest();
    $request->setUserResolver(fn () => $user);

    $validator = Validator::make($data, $request->rules());

    $request->merge($data);
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('temp_brand'))->toBeTrue();
});

test('validation passes for valid data', function () {
    $user = User::factory()->create();

    // Create required dependencies
    $brand = Brands::factory()->create();
    $model = CarModels::factory()->create(['id_marca' => $brand->id]);
    $fuel = Fuels::factory()->create();
    $gear = Gears::factory()->create();
    $listingType = ListingType::factory()->create();
    $color = Color::factory()->create();

    $data = [
        'id_marca' => $brand->id,
        'id_modelo' => $model->id,
        'id_combustible' => $fuel->id,
        'id_marcha' => $gear->id,
        'precio' => 10000,
        'anyo_matri' => 2022,
        'km' => 5000,
        'matricula' => '1234XYZ',
        'id_color' => $color->id,
        'descripcion' => 'Valid car',
        'id_listing_type' => $listingType->id
    ];

    $request = new StoreCarRequest();
    $request->setUserResolver(fn () => $user);

    $validator = Validator::make($data, $request->rules());
    $request->merge($data);
    $request->withValidator($validator);

    if ($validator->fails()) {
        // Debugging helper: print errors if it fails unexpectedly
        // dump($validator->errors()->all());
    }

    expect($validator->passes())->toBeTrue();
});
