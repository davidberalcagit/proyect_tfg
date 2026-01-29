<?php

namespace Tests\Feature\Jobs;

use App\Jobs\AuditCarPricesJob;
use App\Models\Cars;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('audit car prices job logs high and low prices', function () {
    Log::spy();

    // Create cars with various prices
    $cheapCar = Cars::factory()->create(['precio' => 500, 'title' => 'Cheap Car']);
    $expensiveCar = Cars::factory()->create(['precio' => 150000, 'title' => 'Expensive Car']);
    $normalCar = Cars::factory()->create(['precio' => 20000, 'title' => 'Normal Car']);

    $job = new AuditCarPricesJob();
    $job->handle();

    Log::shouldHaveReceived('warning')->with("Precio sospechosamente bajo: {$cheapCar->id} - {$cheapCar->precio}");
    Log::shouldHaveReceived('info')->with("Precio alto detectado: {$expensiveCar->id} - {$expensiveCar->precio}");
    // Normal car should not trigger log (assuming logic)
});
