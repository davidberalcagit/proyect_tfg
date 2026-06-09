<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(\Database\Seeders\TestDatabaseSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('individual');
    $this->customer = Customers::factory()->create(['id_usuario' => $this->user->id]);
});

test('car show displays external image correctly', function () {
    $car = Cars::factory()->create([
        'id_vendedor' => $this->customer->id,
        'image' => 'https://example.com/car.jpg'
    ]);

    $response = $this->actingAs($this->user)->get(route('cars.show', $car));

    $response->assertOk();
    $response->assertSee('src="https://example.com/car.jpg"', false);
});

test('car show displays local image correctly', function () {
    Storage::fake('public');
    $car = Cars::factory()->create([
        'id_vendedor' => $this->customer->id,
        'image' => 'cars/local.jpg'
    ]);

    $response = $this->actingAs($this->user)->get(route('cars.show', $car));

    $response->assertOk();
    $expectedUrl = Storage::url('cars/local.jpg');
    $response->assertSee('src="' . $expectedUrl . '"', false);
});

test('car show displays fallback when no image', function () {
    $car = Cars::factory()->create([
        'id_vendedor' => $this->customer->id,
        'image' => null
    ]);

    $response = $this->actingAs($this->user)->get(route('cars.show', $car));

    $response->assertOk();
    $response->assertSee('No Image');
});
