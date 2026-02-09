<?php

use App\Models\Cars;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('index displays favorites', function () {
    $user = User::factory()->create();
    $car = Cars::factory()->create();
    $user->favorites()->attach($car->id);

    $response = $this->actingAs($user)->get(route('favorites.index'));

    $response->assertStatus(200);
    $response->assertSee($car->title);
});

test('toggle adds to favorites', function () {
    $user = User::factory()->create();
    $car = Cars::factory()->create();

    $response = $this->actingAs($user)->post(route('favorites.toggle', $car));

    $response->assertRedirect();
    $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'car_id' => $car->id]);
});

test('toggle removes from favorites', function () {
    $user = User::factory()->create();
    $car = Cars::factory()->create();
    $user->favorites()->attach($car->id);

    $response = $this->actingAs($user)->post(route('favorites.toggle', $car));

    $response->assertRedirect();
    $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'car_id' => $car->id]);
});

test('toggle returns json', function () {
    $user = User::factory()->create();
    $car = Cars::factory()->create();

    $response = $this->actingAs($user)->postJson(route('favorites.toggle', $car));

    $response->assertStatus(200)
             ->assertJson(['attached' => true]);
});
