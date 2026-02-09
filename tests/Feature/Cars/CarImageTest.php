<?php

use App\Models\Cars;
use Illuminate\Support\Facades\Storage;

test('car show displays external image correctly', function () {
    $car = Cars::factory()->create([
        'image' => 'https://example.com/car.jpg'
    ]);

    $response = $this->get(route('cars.show', $car));

    $response->assertOk();
    $response->assertSee('src="https://example.com/car.jpg"', false);
});

test('car show displays local image correctly', function () {
    Storage::fake('public');
    $car = Cars::factory()->create([
        'image' => 'cars/local.jpg'
    ]);

    $response = $this->get(route('cars.show', $car));

    $response->assertOk();
    // Storage::url('cars/local.jpg') usually returns /storage/cars/local.jpg
    $expectedUrl = Storage::url('cars/local.jpg');
    $response->assertSee('src="' . $expectedUrl . '"', false);
});

test('car show displays fallback when no image', function () {
    $car = Cars::factory()->create([
        'image' => null
    ]);

    $response = $this->get(route('cars.show', $car));

    $response->assertOk();
    $response->assertSee('No Image');
});
