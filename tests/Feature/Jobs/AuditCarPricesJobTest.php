<?php

namespace Tests\Feature\Jobs;

use App\Jobs\AuditCarPricesJob;
use App\Models\Cars;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('audit car prices job logs high and low prices', function () {
    Log::spy();

    $cheapCar = Cars::factory()->create(['precio' => 0, 'title' => 'Free Car']);
    $normalCar = Cars::factory()->create(['precio' => 20000, 'title' => 'Normal Car']);

    $job = new AuditCarPricesJob();
    $job->handle();

    Log::shouldHaveReceived('warning')->with("Coche ID {$cheapCar->id} tiene un precio sospechoso: {$cheapCar->precio} €. Marcando para revisión.");
    Log::shouldHaveReceived('info')->with("Iniciando auditoría de precios de coches...");
});
