<?php

namespace Tests\Feature\Models;

use App\Models\Cars;
use App\Models\CarStatus;
use App\Models\Customers;
use App\Models\Sales;
use App\Models\SaleStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Asegurarnos de que los estados de los coches y ventas existen para las foreign keys
    CarStatus::firstOrCreate(['id' => 1, 'nombre' => 'En Venta']);
    CarStatus::firstOrCreate(['id' => 2, 'nombre' => 'Vendido']);
    SaleStatus::firstOrCreate(['id' => 1, 'nombre' => 'Completada']);
});

test('scope active traders filters users correctly', function () {
    // 1. Usuario inactivo (sin coches ni ventas recientes)
    $inactiveUser = User::factory()->create();
    Customers::factory()->create(['id_usuario' => $inactiveUser->id]);

    // 2. Usuario activo por tener un coche en venta (creado hace menos de un año)
    $activeUserByCar = User::factory()->create();
    $activeCustomerCar = Customers::factory()->create(['id_usuario' => $activeUserByCar->id]);

    Cars::factory()->create([
        'id_vendedor' => $activeCustomerCar->id,
        'id_estado' => 1,
        'created_at' => now()->subMonths(6)->toDateTimeString()
    ]);

    // 3. Usuario INACTIVO porque su coche en venta es muy antiguo (más de 1 año)
    $inactiveUserOldCar = User::factory()->create();
    $inactiveCustomerCar = Customers::factory()->create(['id_usuario' => $inactiveUserOldCar->id]);
    Cars::factory()->create([
        'id_vendedor' => $inactiveCustomerCar->id,
        'id_estado' => 1,
        'created_at' => '2010-01-01 00:00:00'
    ]);

    // 4. Usuario activo por tener una venta reciente (menos de 30 días)
    $activeUserBySale = User::factory()->create();
    $activeCustomerSale = Customers::factory()->create(['id_usuario' => $activeUserBySale->id]);
    $carForSale = Cars::factory()->create([
        'id_vendedor' => $activeCustomerSale->id,
        'id_estado' => 2,
        'created_at' => '2010-01-01 00:00:00'
    ]);
    $buyer = Customers::factory()->create();

    DB::table('sales')->insert([
        'id_vehiculo' => $carForSale->id,
        'id_vendedor' => $activeCustomerSale->id,
        'id_comprador' => $buyer->id,
        'precio' => 10000,
        'id_estado' => 1,
        'created_at' => now()->subDays(10)->toDateTimeString(),
        'updated_at' => now()->subDays(10)->toDateTimeString()
    ]);

    // 5. Usuario INACTIVO por tener una venta antigua (más de 30 días)
    $inactiveUserOldSale = User::factory()->create();
    $inactiveCustomerOldSale = Customers::factory()->create(['id_usuario' => $inactiveUserOldSale->id]);
    $carForOldSale = Cars::factory()->create([
        'id_vendedor' => $inactiveCustomerOldSale->id,
        'id_estado' => 2,
        'created_at' => '2010-01-01 00:00:00'
    ]);

    DB::table('sales')->insert([
        'id_vehiculo' => $carForOldSale->id,
        'id_vendedor' => $inactiveCustomerOldSale->id,
        'id_comprador' => $buyer->id,
        'precio' => 10000,
        'id_estado' => 1,
        'created_at' => '2010-01-01 00:00:00',
        'updated_at' => '2010-01-01 00:00:00'
    ]);

    // EJECUCIÓN DEL SCOPE
    $activeTraders = User::activeTraders()->pluck('id')->toArray();

    // COMPROBACIONES
    // Deberían estar los activos:
    expect($activeTraders)->toContain($activeUserByCar->id);
    expect($activeTraders)->toContain($activeUserBySale->id);

    // NO deberían estar los inactivos:
    expect($activeTraders)->not->toContain($inactiveUser->id);
    expect($activeTraders)->not->toContain($inactiveUserOldCar->id);
    expect($activeTraders)->not->toContain($inactiveUserOldSale->id);

    // En total solo debería haber 2 usuarios activos en este test
    expect(count($activeTraders))->toBe(2);
});
