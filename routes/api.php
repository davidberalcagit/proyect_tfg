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

// Ruta pública para obtener el token
Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes
Route::apiResource('brands', BrandsController::class)->only(['index', 'show'])->names('api.brands');
Route::get('brands/{id}/models', [BrandsController::class, 'models']);

Route::apiResource('car-models', CarModelsController::class)->only(['index', 'show'])->names('api.car-models');

// Coches públicos (lectura)
Route::apiResource('cars', CarsController::class)->only(['index', 'show'])->names('api.cars');

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas de Clientes (Perfil de vendedor)
    Route::get('/customers/me', [CustomersController::class, 'me']);
    Route::apiResource('customers', CustomersController::class)->names('api.customers');

    // Coches protegidos (escritura)
    Route::apiResource('cars', CarsController::class)->except(['index', 'show'])->names('api.cars');

    Route::apiResource('sales', SalesController::class)->names('api.sales');
    Route::apiResource('offers', OfferController::class)->names('api.offers');

    // Admin-only routes for Brands and Models
    Route::apiResource('brands', BrandsController::class)->except(['index', 'show'])->names('api.brands');
    Route::apiResource('car-models', CarModelsController::class)->except(['index', 'show'])->names('api.car-models');
});
