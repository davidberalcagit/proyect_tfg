<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Sales;
use App\Models\SaleStatus;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->sellerUser = User::factory()->create();
    $this->seller = Customers::factory()->create(['id_usuario' => $this->sellerUser->id]);

    $this->buyerUser = User::factory()->create();
    $this->buyer = Customers::factory()->create(['id_usuario' => $this->buyerUser->id]);

    // Ensure car is 'En Venta' (id_estado = 1)
    $this->car = Cars::factory()->create([
        'id_vendedor' => $this->seller->id,
        'id_estado' => 1
    ]);

    $this->status = SaleStatus::firstOrCreate(['id' => 1], ['nombre' => 'Completada']);
});

test('api sales index returns user sales', function () {
    Sales::factory()->create(['id_vendedor' => $this->seller->id]);
    Sales::factory()->create(['id_comprador' => $this->seller->id]);

    // Create unrelated sale with distinct users
    $otherUser = User::factory()->create();
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id]);
    $otherSellerUser = User::factory()->create();
    $otherSellerCustomer = Customers::factory()->create(['id_usuario' => $otherSellerUser->id]);

    Sales::factory()->create([
        'id_vendedor' => $otherSellerCustomer->id,
        'id_comprador' => $otherCustomer->id
    ]);

    Sanctum::actingAs($this->sellerUser);

    $response = $this->getJson(route('api.sales.index'));

    $response->assertStatus(200)
             ->assertJsonCount(2, 'data');
});

test('api sales store creates sale', function () {
    Sanctum::actingAs($this->sellerUser);

    $response = $this->postJson(route('api.sales.store'), [
        'id_vehiculo' => $this->car->id,
        'id_comprador' => $this->buyer->id,
        'precio' => 15000,
        'fecha' => now()->toDateString(),
        'metodo_pago' => 'Transferencia',
        'estado' => $this->status->id
    ]);

    $response->assertStatus(201)
             ->assertJson(['precio' => 15000]);

    $this->assertDatabaseHas('sales', [
        'id_vehiculo' => $this->car->id,
        'id_vendedor' => $this->seller->id,
        'id_comprador' => $this->buyer->id
    ]);
});

test('api sales store forbids selling not owned car', function () {
    // Ensure other car belongs to someone else
    $otherUser = User::factory()->create();
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id]);
    $otherCar = Cars::factory()->create([
        'id_vendedor' => $otherCustomer->id,
        'id_estado' => 1
    ]);

    Sanctum::actingAs($this->sellerUser);

    $response = $this->postJson(route('api.sales.store'), [
        'id_vehiculo' => $otherCar->id,
        'id_comprador' => $this->buyer->id,
        'precio' => 15000,
        'fecha' => now()->toDateString(),
        'metodo_pago' => 'Transferencia',
        'estado' => $this->status->id
    ]);

    $response->assertStatus(403);
});

test('api sales show returns sale details', function () {
    $sale = Sales::factory()->create([
        'id_vendedor' => $this->seller->id,
        'id_comprador' => $this->buyer->id
    ]);

    Sanctum::actingAs($this->buyerUser);

    $response = $this->getJson(route('api.sales.show', $sale->id));

    $response->assertStatus(200)
             ->assertJson(['id' => $sale->id]);
});

test('api sales show forbids unrelated user', function () {
    $sale = Sales::factory()->create();
    $otherUser = User::factory()->create();
    Customers::factory()->create(['id_usuario' => $otherUser->id]);

    Sanctum::actingAs($otherUser);

    $response = $this->getJson(route('api.sales.show', $sale->id));

    $response->assertStatus(403);
});

test('api sales update allows seller to modify', function () {
    $sale = Sales::factory()->create([
        'id_vendedor' => $this->seller->id,
        'precio' => 10000
    ]);

    Sanctum::actingAs($this->sellerUser);

    $response = $this->putJson(route('api.sales.update', $sale->id), [
        'precio' => 12000
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('sales', ['id' => $sale->id, 'precio' => 12000]);
});

test('api sales update forbids buyer to modify', function () {
    $sale = Sales::factory()->create([
        'id_vendedor' => $this->seller->id,
        'id_comprador' => $this->buyer->id
    ]);

    Sanctum::actingAs($this->buyerUser);

    $response = $this->putJson(route('api.sales.update', $sale->id), [
        'precio' => 12000
    ]);

    $response->assertStatus(403);
});

test('api sales destroy deletes sale by seller', function () {
    $sale = Sales::factory()->create(['id_vendedor' => $this->seller->id]);

    Sanctum::actingAs($this->sellerUser);

    $response = $this->deleteJson(route('api.sales.destroy', $sale->id));

    $response->assertStatus(204);
    $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
});
