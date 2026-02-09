<?php

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Database\Eloquent\Factories\Sequence;

test('api car models index returns list', function () {
    CarModels::factory()
        ->count(3)
        ->state(new Sequence(
            fn ($sequence) => ['nombre' => 'Model ' . uniqid()],
        ))
        ->create();

    $response = $this->getJson(route('api.car-models.index'));

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

test('api car models store creates model', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $brand = Brands::factory()->create();

    $response = $this->postJson(route('api.car-models.store'), [
        'nombre' => 'New Model ' . uniqid(),
        'id_marca' => $brand->id
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('car_models', ['nombre' => $response->json('nombre')]);
});

test('api car models show returns model', function () {
    $model = CarModels::factory()->create(['nombre' => 'Show Model ' . uniqid()]);

    $response = $this->getJson(route('api.car-models.show', $model->id));

    $response->assertStatus(200)
             ->assertJson(['id' => $model->id]);
});

test('api car models update modifies model', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $model = CarModels::factory()->create(['nombre' => 'Update Model ' . uniqid()]);

    $newName = 'Updated Model ' . uniqid();
    $response = $this->putJson(route('api.car-models.update', $model->id), [
        'nombre' => $newName
    ]);

    $response->assertStatus(200)
             ->assertJson(['nombre' => $newName]);

    $this->assertDatabaseHas('car_models', ['id' => $model->id, 'nombre' => $newName]);
});

test('api car models destroy deletes model', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $model = CarModels::factory()->create(['nombre' => 'Destroy Model ' . uniqid()]);

    $response = $this->deleteJson(route('api.car-models.destroy', $model->id));

    $response->assertStatus(204);
    $this->assertDatabaseMissing('car_models', ['id' => $model->id]);
});
