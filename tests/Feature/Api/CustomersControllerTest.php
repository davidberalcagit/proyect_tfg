<?php

use App\Models\Customers;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('api can list customers', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    Customers::factory()->count(3)->create();

    $response = $this->getJson('/api/customers');

    $response->assertStatus(200);
});

test('api can create customer', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/customers', [
        'nombre_contacto' => 'API Customer',
        'telefono' => '600999888',
        'id_entidad' => 1,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('customers', ['nombre_contacto' => 'API Customer']);
});

test('api can show customer', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $customer = Customers::factory()->create();

    $response = $this->getJson("/api/customers/{$customer->id}");

    $response->assertStatus(200)
             ->assertJson(['id' => $customer->id]);
});

test('api can update customer', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson("/api/customers/{$customer->id}", [
        'nombre_contacto' => 'Updated Name',
        'telefono' => '600111222'
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'nombre_contacto' => 'Updated Name']);
});

test('api cannot update other customer', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $otherUser->id]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson("/api/customers/{$customer->id}", [
        'nombre_contacto' => 'Hacked Name'
    ]);

    $response->assertStatus(403);
});

test('api can delete customer', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->deleteJson("/api/customers/{$customer->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});
