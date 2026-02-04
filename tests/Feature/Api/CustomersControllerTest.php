<?php

namespace Tests\Feature\Api;

use App\Models\Customers;
use App\Models\Dealerships;
use App\Models\EntityType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    EntityType::firstOrCreate(['id' => 1], ['nombre' => 'Particular']);
    EntityType::firstOrCreate(['id' => 2], ['nombre' => 'Concesionario']);
});

test('api customers me returns current customer', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    Sanctum::actingAs($user);

    $this->getJson('/api/customers/me')
         ->assertStatus(200)
         ->assertJson(['id' => $customer->id]);
});

test('api customers me returns 404 if no customer profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson('/api/customers/me')
         ->assertStatus(404);
});

test('api customers index lists customers', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    Customers::factory()->count(3)->create();

    $this->getJson(route('api.customers.index'))
         ->assertStatus(200)
         ->assertJsonCount(3, 'data');
});

test('api customers store creates individual profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'id_entidad' => 1, // Particular
        'nombre_contacto' => 'Test User',
        'telefono' => '123456789',
    ];

    $this->postJson(route('api.customers.store'), $data)
         ->assertStatus(201)
         ->assertJsonFragment(['nombre_contacto' => 'Test User']);

    $this->assertDatabaseHas('customers', ['id_usuario' => $user->id]);
});

test('api customers store creates dealership profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'id_entidad' => 2, // Concesionario
        'nombre_contacto' => 'Dealer User',
        'telefono' => '987654321',
        'nombre_empresa' => 'Auto Corp',
        'nif' => 'B12345678',
        'direccion' => 'Main St 1',
    ];

    $this->postJson(route('api.customers.store'), $data)
         ->assertStatus(201);

    $this->assertDatabaseHas('dealerships', ['nif' => 'B12345678']);
});

test('api customers store fails if profile exists', function () {
    $user = User::factory()->create();
    Customers::factory()->create(['id_usuario' => $user->id]);
    Sanctum::actingAs($user);

    $data = [
        'id_entidad' => 1,
        'nombre_contacto' => 'Test User',
        'telefono' => '123456789',
    ];

    $this->postJson(route('api.customers.store'), $data)
         ->assertStatus(409);
});

test('api customers show returns customer details', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $customer = Customers::factory()->create();

    $this->getJson(route('api.customers.show', $customer->id))
         ->assertStatus(200)
         ->assertJson(['id' => $customer->id]);
});

test('api customers update modifies customer', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    Sanctum::actingAs($user);

    $this->putJson(route('api.customers.update', $customer->id), ['nombre_contacto' => 'New Name'])
         ->assertStatus(200);

    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'nombre_contacto' => 'New Name']);
});

test('api customers update forbids if not owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $otherUser->id]);
    Sanctum::actingAs($user);

    $this->putJson(route('api.customers.update', $customer->id), ['nombre_contacto' => 'Hacker'])
         ->assertStatus(403);
});

test('api customers update validates input', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    Sanctum::actingAs($user);

    $this->putJson(route('api.customers.update', $customer->id), ['telefono' => 'toolongphonenumber123456789'])
         ->assertStatus(422);
});

test('api customers destroy deletes customer', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    Sanctum::actingAs($user);

    $this->deleteJson(route('api.customers.destroy', $customer->id))
         ->assertStatus(204);

    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});

test('api customers destroy forbids if not owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $otherUser->id]);
    Sanctum::actingAs($user);

    $this->deleteJson(route('api.customers.destroy', $customer->id))
         ->assertStatus(403);
});
