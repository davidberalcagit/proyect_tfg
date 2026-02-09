<?php

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('api brands index returns list', function () {
    Brands::factory()->count(3)->create();

    $response = $this->getJson(route('api.brands.index'));

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

test('api brands store creates brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.brands.store'), [
        'nombre' => 'New Brand ' . uniqid()
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('brands', ['nombre' => $response->json('nombre')]);
});

test('api brands show returns brand', function () {
    $brand = Brands::factory()->create();

    $response = $this->getJson(route('api.brands.show', $brand->id));

    $response->assertStatus(200)
             ->assertJson(['id' => $brand->id]);
});

test('api brands update modifies brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $brand = Brands::factory()->create();

    $newName = 'Updated Brand ' . uniqid();
    $response = $this->putJson(route('api.brands.update', $brand->id), [
        'nombre' => $newName
    ]);

    $response->assertStatus(200)
             ->assertJson(['nombre' => $newName]);

    $this->assertDatabaseHas('brands', ['id' => $brand->id, 'nombre' => $newName]);
});

test('api brands destroy deletes brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $brand = Brands::factory()->create();

    $response = $this->deleteJson(route('api.brands.destroy', $brand->id));

    $response->assertStatus(204);
    $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
});

test('api brands models returns associated models', function () {
    $brand = Brands::factory()->create();
    CarModels::factory()->create(['id_marca' => $brand->id, 'nombre' => 'Model A ' . uniqid()]);
    CarModels::factory()->create(['id_marca' => $brand->id, 'nombre' => 'Model B ' . uniqid()]);

    $otherBrand = Brands::factory()->create();
    CarModels::factory()->create(['id_marca' => $otherBrand->id, 'nombre' => 'Model C ' . uniqid()]);

    $response = $this->getJson(route('api.brands.models', $brand->id));

    $response->assertStatus(200)
             ->assertJsonCount(2);
});
