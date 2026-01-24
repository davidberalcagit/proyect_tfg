<?php

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\Color;
use App\Models\ListingType;
use App\Models\CarStatus;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->brand = Brands::factory()->create(['nombre' => 'Toyota ' . uniqid()]);
    $this->model = CarModels::factory()->create(['id_marca' => $this->brand->id, 'nombre' => 'Corolla ' . uniqid()]);

    // Create auxiliary data
    $this->fuel = Fuels::firstOrCreate(['nombre' => 'Gasolina']);
    $this->gear = Gears::firstOrCreate(['tipo' => 'Manual']);
    $this->color = Color::firstOrCreate(['nombre' => 'Rojo']);
    $this->listingType = ListingType::firstOrCreate(['nombre' => 'Venta']);

    // Ensure CarStatus exists
    CarStatus::firstOrCreate(['id' => 1], ['nombre' => 'En Venta']);
    CarStatus::firstOrCreate(['id' => 4], ['nombre' => 'Pendiente']);
});

test('api index returns available cars', function () {
    Cars::factory()->count(3)->create(['id_estado' => 1]); // Available
    Cars::factory()->create(['id_estado' => 4]); // Pending

    $response = $this->getJson(route('api.cars.index'));

    $response->assertStatus(200)
             ->assertJsonCount(3, 'data');
});

test('api store creates car', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    Sanctum::actingAs($user);

    // Setup permission
    Permission::firstOrCreate(['name' => 'create cars', 'guard_name' => 'web']);
    $user->givePermissionTo('create cars');

    $data = [
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->model->id,
        'matricula' => '1234ABC',
        'anyo_matri' => 2020,
        'km' => 50000,
        'precio' => 10000,
        'descripcion' => 'Test car',
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
    ];

    $response = $this->postJson(route('api.cars.store'), $data);

    $response->assertStatus(201)
             ->assertJsonFragment(['message' => 'Coche creado correctamente. Está pendiente de revisión por un supervisor.']);

    $this->assertDatabaseHas('cars', [
        'matricula' => '1234ABC',
        'id_vendedor' => $customer->id,
        'id_estado' => 4
    ]);
});

test('api store forbids non-customer users', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Permission::firstOrCreate(['name' => 'create cars', 'guard_name' => 'web']);
    $user->givePermissionTo('create cars');

    $response = $this->postJson(route('api.cars.store'), []);

    $response->assertStatus(403);
});

test('api show returns car details', function () {
    $car = Cars::factory()->create();

    $response = $this->getJson(route('api.cars.show', $car->id));

    $response->assertStatus(200)
             ->assertJson(['id' => $car->id]);
});

test('api update modifies car if owner and pending', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 4, // Pending
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
    ]);

    Sanctum::actingAs($user);

    // Send ALL required fields
    $response = $this->putJson(route('api.cars.update', $car->id), [
        'precio' => 12000,
        'id_marca' => $car->id_marca,
        'id_modelo' => $car->id_modelo,
        'id_combustible' => $car->id_combustible,
        'id_marcha' => $car->id_marcha,
        'anyo_matri' => $car->anyo_matri,
        'km' => $car->km,
        'matricula' => $car->matricula,
        'id_color' => $car->id_color,
        'descripcion' => $car->descripcion,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('cars', ['id' => $car->id, 'precio' => 12000]);
});

test('api update forbids if not owner', function () {
    $user = User::factory()->create();
    Customers::factory()->create(['id_usuario' => $user->id]);

    $otherUser = User::factory()->create();
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $otherCustomer->id,
        'id_estado' => 4
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson(route('api.cars.update', $car->id), ['precio' => 12000]);

    $response->assertStatus(403);
});

test('api update forbids if car is approved', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 1 // Approved
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson(route('api.cars.update', $car->id), ['precio' => 12000]);

    $response->assertStatus(403);
});

test('api destroy deletes car if owner', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('api.cars.destroy', $car->id));

    $response->assertStatus(204);
    $this->assertDatabaseMissing('cars', ['id' => $car->id]);
});

test('api myCars returns user cars', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    Cars::factory()->create(['id_vendedor' => $customer->id]);
    Cars::factory()->create(['id_vendedor' => $customer->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson(route('api.cars.myCars'));

    $response->assertStatus(200)
             ->assertJsonCount(2);
});
