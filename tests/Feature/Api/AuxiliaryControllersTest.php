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
    $fuels = Fuels::factory()->count(3)->create();
    $response = $this->getJson(route('api.fuels.index'))
         ->assertStatus(200);
    foreach ($fuels as $fuel) {
        $response->assertJsonFragment(['id' => $fuel->id]);
    }
});

test('api fuels store creates fuel', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.fuels.store'), ['nombre' => 'Hydrogen', 'emission_type' => 'ZERO'])
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

    $this->putJson(route('api.fuels.update', $fuel->id), ['nombre' => 'Updated Fuel', 'emission_type' => 'ECO'])
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
    $colors = Color::factory()->count(3)->create();
    $response = $this->getJson(route('api.colors.index'))
         ->assertStatus(200);

    foreach ($colors as $color) {
        $response->assertJsonFragment(['id' => $color->id]);
    }
});

test('api colors store creates color', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.colors.store'), ['nombre' => 'Magenta', 'hex_code' => '#FF00FF'])
         ->assertStatus(201);
    $this->assertDatabaseHas('colors', ['nombre' => 'Magenta']);
});

test('api colors update modifies color', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $color = Color::factory()->create();

    $this->putJson(route('api.colors.update', $color->id), ['nombre' => 'Cyan', 'hex_code' => '#00FFFF'])
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
    $gears = collect([
        Gears::firstOrCreate(['tipo' => 'Manual']),
        Gears::firstOrCreate(['tipo' => 'Automático']),
    ]);

    $response = $this->getJson(route('api.gears.index'))
         ->assertStatus(200);

    foreach ($gears as $gear) {
        $response->assertJsonFragment(['id' => $gear->id]);
    }
});

test('api gears store creates gear', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.gears.store'), ['tipo' => 'CVT', 'speed_count' => 7])
         ->assertStatus(201);
    $this->assertDatabaseHas('gears', ['tipo' => 'CVT']);
});

test('api gears update modifies gear', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $gear = Gears::factory()->create();

    $this->putJson(route('api.gears.update', $gear->id), ['tipo' => 'DSG', 'speed_count' => 7])
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
