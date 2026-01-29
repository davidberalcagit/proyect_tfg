<?php

use App\Models\Color;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

// --- Fuels ---
test('api fuels index returns list', function () {
    Fuels::factory()->count(3)->create();
    $this->getJson(route('api.fuels.index'))
         ->assertStatus(200)
         ->assertJsonCount(3);
});

test('api fuels store creates fuel', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.fuels.store'), ['nombre' => 'Hydrogen'])
         ->assertStatus(201);
    $this->assertDatabaseHas('fuels', ['nombre' => 'Hydrogen']);
});

test('api fuels show returns fuel', function () {
    $fuel = Fuels::factory()->create();
    $this->getJson(route('api.fuels.show', $fuel->id))
         ->assertStatus(200)
         ->assertJson(['id' => $fuel->id]);
});

test('api fuels update modifies fuel', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $fuel = Fuels::factory()->create();

    $this->putJson(route('api.fuels.update', $fuel->id), ['nombre' => 'Updated Fuel'])
         ->assertStatus(200);
    $this->assertDatabaseHas('fuels', ['id' => $fuel->id, 'nombre' => 'Updated Fuel']);
});

test('api fuels destroy deletes fuel', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $fuel = Fuels::factory()->create();

    $this->deleteJson(route('api.fuels.destroy', $fuel->id))
         ->assertStatus(204);
    $this->assertDatabaseMissing('fuels', ['id' => $fuel->id]);
});

// --- Colors ---
test('api colors index returns list', function () {
    Color::factory()->count(3)->create();
    $this->getJson(route('api.colors.index'))
         ->assertStatus(200)
         ->assertJsonCount(3);
});

test('api colors store creates color', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.colors.store'), ['nombre' => 'Magenta'])
         ->assertStatus(201);
    $this->assertDatabaseHas('colors', ['nombre' => 'Magenta']);
});

// --- Gears ---
test('api gears index returns list', function () {
    Gears::factory()
        ->count(2)
        ->state(new Sequence(['tipo' => 'Manual'], ['tipo' => 'Automático']))
        ->create();

    $this->getJson(route('api.gears.index'))
         ->assertStatus(200)
         ->assertJsonCount(2);
});

test('api gears store creates gear', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.gears.store'), ['tipo' => 'CVT'])
         ->assertStatus(201);
    $this->assertDatabaseHas('gears', ['tipo' => 'CVT']);
});

// --- Vehicle (Placeholder) ---
test('api vehicle controller index returns empty', function () {
    $this->getJson(route('api.vehicles.index'))
         ->assertStatus(200)
         ->assertJson([]);
});
