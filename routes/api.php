<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarsController;
use App\Http\Controllers\Api\BrandsController;
use App\Http\Controllers\Api\CarModelsController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomersController;
use App\Http\Controllers\Api\FuelsController;
use App\Http\Controllers\Api\ColorsController;
use App\Http\Controllers\Api\GearsController;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('brands', BrandsController::class)->only(['index', 'show'])->names('api.brands');
Route::get('brands/{id}/models', [BrandsController::class, 'models'])->name('api.brands.models');

Route::apiResource('car-models', CarModelsController::class)->only(['index', 'show'])->names('api.car-models');
Route::apiResource('fuels', FuelsController::class)->only(['index', 'show'])->names('api.fuels');
Route::apiResource('colors', ColorsController::class)->only(['index', 'show'])->names('api.colors');
Route::apiResource('gears', GearsController::class)->only(['index', 'show'])->names('api.gears');

Route::apiResource('cars', CarsController::class)->only(['index', 'show'])->names('api.cars');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/my-cars', [CarsController::class, 'myCars'])->name('api.cars.myCars');

    Route::get('/customers/me', [CustomersController::class, 'me']);
    Route::apiResource('customers', CustomersController::class)->names('api.customers');

    Route::apiResource('cars', CarsController::class)->except(['index', 'show'])->names('api.cars');

    Route::apiResource('sales', SalesController::class)->names('api.sales');
    Route::apiResource('offers', OfferController::class)->names('api.offers');

    Route::apiResource('brands', BrandsController::class)->except(['index', 'show'])->names('api.brands');
    Route::apiResource('car-models', CarModelsController::class)->except(['index', 'show'])->names('api.car-models');
    Route::apiResource('fuels', FuelsController::class)->except(['index', 'show'])->names('api.fuels');
    Route::apiResource('colors', ColorsController::class)->except(['index', 'show'])->names('api.colors');
    Route::apiResource('gears', GearsController::class)->except(['index', 'show'])->names('api.gears');
});
