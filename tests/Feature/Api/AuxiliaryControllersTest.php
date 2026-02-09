<?php

use App\Models\Color;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

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

test('api colors update modifies color', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $color = Color::factory()->create();

    $this->putJson(route('api.colors.update', $color->id), ['nombre' => 'Cyan'])
         ->assertStatus(200);
    $this->assertDatabaseHas('colors', ['id' => $color->id, 'nombre' => 'Cyan']);
});

test('api colors destroy deletes color', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $color = Color::factory()->create();

    $this->deleteJson(route('api.colors.destroy', $color->id))
         ->assertStatus(204);
    $this->assertDatabaseMissing('colors', ['id' => $color->id]);
});

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

test('api gears update modifies gear', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $gear = Gears::factory()->create();

    $this->putJson(route('api.gears.update', $gear->id), ['tipo' => 'DSG'])
         ->assertStatus(200);
    $this->assertDatabaseHas('gears', ['id' => $gear->id, 'tipo' => 'DSG']);
});

test('api gears destroy deletes gear', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $gear = Gears::factory()->create();

    $this->deleteJson(route('api.gears.destroy', $gear->id))
         ->assertStatus(204);
    $this->assertDatabaseMissing('gears', ['id' => $gear->id]);
});
