<?php

use App\Jobs\SendCarApprovedNotificationJob;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\ListingType;
use Illuminate\Support\Facades\Queue;

test('command approves car and processes temp data', function () {
    Queue::fake();

    $listingType = ListingType::factory()->create(['nombre' => 'Venta']);

    $car = Cars::factory()->create([
        'id_estado' => 4,
        'temp_brand' => 'New Brand',
        'temp_model' => 'New Model',
        'temp_color' => 'New Color',
        'id_listing_type' => $listingType->id
    ]);

    $this->artisan('cars:approve', ['car_id' => $car->id])
         ->expectsOutput("Marca temporal 'New Brand' procesada.")
         ->expectsOutput("Modelo temporal 'New Model' procesado.")
         ->expectsOutput("Color temporal 'New Color' procesado.")
         ->assertExitCode(0);

    $car->refresh();

    expect($car->id_estado)->toBe(1);
    expect($car->temp_brand)->toBeNull();
    expect($car->temp_model)->toBeNull();
    expect($car->temp_color)->toBeNull();

    $this->assertDatabaseHas('brands', ['nombre' => 'New Brand']);
    $this->assertDatabaseHas('car_models', ['nombre' => 'New Model']);
    $this->assertDatabaseHas('colors', ['nombre' => 'New Color']);

    Queue::assertPushed(SendCarApprovedNotificationJob::class);
});

test('command fails if car not found', function () {
    $this->artisan('cars:approve', ['car_id' => 99999])
         ->expectsOutput("Coche con ID 99999 no encontrado.")
         ->assertExitCode(1);
});
