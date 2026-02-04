<?php

namespace Tests\Feature\Controllers;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\CarStatus;
use App\Models\Color;
use App\Models\Customers;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'individual']);

    DB::table('rental_statuses')->insertOrIgnore([
        ['id' => 1, 'nombre' => 'Pendiente de Aprobación'],
        ['id' => 2, 'nombre' => 'En espera de entrega'],
        ['id' => 3, 'nombre' => 'Usando'],
        ['id' => 6, 'nombre' => 'Rechazado'],
        ['id' => 7, 'nombre' => 'Aceptado por dueño (Esperando pago)'],
    ]);

    $this->brand = Brands::factory()->create();
    $this->model = CarModels::factory()->create(['id_marca' => $this->brand->id]);
    $this->fuel = Fuels::factory()->create();
    $this->gear = Gears::factory()->create();
    $this->color = Color::factory()->create();
    $this->listingType = ListingType::factory()->create();

    CarStatus::firstOrCreate(['id' => 3], ['nombre' => 'En Alquiler']);
    CarStatus::firstOrCreate(['id' => 6], ['nombre' => 'Alquilado']);
});

test('rental create page loads', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->model->id,
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
        'id_estado' => 3
    ]);

    $this->actingAs($user)
         ->get(route('rentals.create', $car->id))
         ->assertStatus(200);
});

test('rental store creates rental request', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->model->id,
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
        'id_estado' => 3
    ]);

    $response = $this->actingAs($user)
         ->post(route('rentals.store', $car->id), [
             'fecha_inicio' => now()->addDay()->format('Y-m-d'),
             'fecha_fin' => now()->addDays(3)->format('Y-m-d'),
         ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertDatabaseHas('rentals', [
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'id_estado' => 1
    ]);
});

test('rental accept updates status', function () {
    $owner = User::factory()->create();
    $owner->assignRole('individual');
    $ownerCustomer = Customers::factory()->create(['id_usuario' => $owner->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $ownerCustomer->id,
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->model->id,
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
    ]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $ownerCustomer->id,
        'fecha_inicio' => now()->addDay(),
        'fecha_fin' => now()->addDays(3),
        'precio_total' => 100,
        'id_estado' => 1
    ]);

    $this->actingAs($owner)
         ->post(route('rentals.accept', $rental->id))
         ->assertRedirect();

    $this->assertDatabaseHas('rentals', ['id' => $rental->id, 'id_estado' => 7]);
});

test('rental reject updates status', function () {
    $owner = User::factory()->create();
    $owner->assignRole('individual');
    $ownerCustomer = Customers::factory()->create(['id_usuario' => $owner->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $ownerCustomer->id,
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->model->id,
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
    ]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $ownerCustomer->id,
        'fecha_inicio' => now()->addDay(),
        'fecha_fin' => now()->addDays(3),
        'precio_total' => 100,
        'id_estado' => 1
    ]);

    $this->actingAs($owner)
         ->post(route('rentals.reject', $rental->id))
         ->assertRedirect();

    $this->assertDatabaseHas('rentals', ['id' => $rental->id, 'id_estado' => 6]);
});

test('rental pay updates status', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->model->id,
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
    ]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => now()->addDay(),
        'fecha_fin' => now()->addDays(3),
        'precio_total' => 100,
        'id_estado' => 7 // Aceptado, esperando pago
    ]);

    $this->actingAs($user)
         ->post(route('rentals.pay', $rental->id))
         ->assertRedirect();

    // Should be 2 (En espera de entrega) if not today, or 3 if today.
    // Since we set start date to tomorrow, it should be 2.
    $this->assertDatabaseHas('rentals', ['id' => $rental->id, 'id_estado' => 2]);
    $this->assertDatabaseHas('cars', ['id' => $car->id, 'id_estado' => 6]); // Alquilado
});

test('rental terms download', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');

    $this->actingAs($user)
         ->get(route('rentals.terms'))
         ->assertStatus(200);
});
