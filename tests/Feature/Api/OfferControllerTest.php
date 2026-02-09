<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->sellerUser = User::factory()->create();
    $this->seller = Customers::factory()->create(['id_usuario' => $this->sellerUser->id]);

    $this->buyerUser = User::factory()->create();
    $this->buyer = Customers::factory()->create(['id_usuario' => $this->buyerUser->id]);

    $this->car = Cars::factory()->create(['id_vendedor' => $this->seller->id]);
});

test('api offer index returns user offers', function () {
    Offer::factory()->create(['id_comprador' => $this->buyer->id]);
    Offer::factory()->create(['id_vendedor' => $this->buyer->id]);

    $otherUser = User::factory()->create();
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id]);
    $otherSellerUser = User::factory()->create();
    $otherSellerCustomer = Customers::factory()->create(['id_usuario' => $otherSellerUser->id]);

    Offer::factory()->create([
        'id_comprador' => $otherCustomer->id,
        'id_vendedor' => $otherSellerCustomer->id
    ]);

    Sanctum::actingAs($this->buyerUser);

    $response = $this->getJson(route('api.offers.index'));

    $response->assertStatus(200)
             ->assertJsonCount(2, 'data');
});

test('api offer store creates offer', function () {
    Sanctum::actingAs($this->buyerUser);

    $response = $this->postJson(route('api.offers.store'), [
        'id_vehiculo' => $this->car->id,
        'precio_oferta' => 9000,
        'mensaje' => 'I want it'
    ]);

    $response->assertStatus(201)
             ->assertJson(['cantidad' => 9000]);

    $this->assertDatabaseHas('offers', [
        'id_vehiculo' => $this->car->id,
        'id_comprador' => $this->buyer->id,
        'cantidad' => 9000
    ]);
});

test('api offer store forbids self offer', function () {
    Sanctum::actingAs($this->sellerUser);

    $response = $this->postJson(route('api.offers.store'), [
        'id_vehiculo' => $this->car->id,
        'precio_oferta' => 9000
    ]);

    $response->assertStatus(400);
});

test('api offer show returns offer details', function () {
    $offer = Offer::factory()->create([
        'id_comprador' => $this->buyer->id,
        'id_vendedor' => $this->seller->id
    ]);

    Sanctum::actingAs($this->buyerUser);

    $response = $this->getJson(route('api.offers.show', $offer->id));

    $response->assertStatus(200)
             ->assertJson(['id' => $offer->id]);
});

test('api offer show forbids unrelated user', function () {
    $offer = Offer::factory()->create();
    $otherUser = User::factory()->create();
    Customers::factory()->create(['id_usuario' => $otherUser->id]);

    Sanctum::actingAs($otherUser);

    $response = $this->getJson(route('api.offers.show', $offer->id));

    $response->assertStatus(403);
});

test('api offer update allows buyer to change price', function () {
    $offer = Offer::factory()->create([
        'id_comprador' => $this->buyer->id,
        'cantidad' => 1000
    ]);

    Sanctum::actingAs($this->buyerUser);

    $response = $this->putJson(route('api.offers.update', $offer->id), [
        'precio_oferta' => 1200
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('offers', ['id' => $offer->id, 'cantidad' => 1200]);
});

test('api offer update allows seller to change status', function () {
    $otherUser = User::factory()->create();
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id]);

    $offer = Offer::factory()->create([
        'id_vendedor' => $this->seller->id,
        'id_comprador' => $otherCustomer->id,
        'estado' => 'pendiente'
    ]);

    Sanctum::actingAs($this->sellerUser);

    $response = $this->putJson(route('api.offers.update', $offer->id), [
        'estado' => 'aceptada'
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('offers', ['id' => $offer->id, 'estado' => 'aceptada']);
});

test('api offer destroy deletes offer by buyer', function () {
    $offer = Offer::factory()->create(['id_comprador' => $this->buyer->id]);

    Sanctum::actingAs($this->buyerUser);

    $response = $this->deleteJson(route('api.offers.destroy', $offer->id));

    $response->assertStatus(204);
    $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
});

test('api offer destroy forbids seller deletion', function () {
    $otherUser = User::factory()->create();
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id]);

    $offer = Offer::factory()->create([
        'id_vendedor' => $this->seller->id,
        'id_comprador' => $otherCustomer->id
    ]);

    Sanctum::actingAs($this->sellerUser);

    $response = $this->deleteJson(route('api.offers.destroy', $offer->id));

    $response->assertStatus(403);
});
