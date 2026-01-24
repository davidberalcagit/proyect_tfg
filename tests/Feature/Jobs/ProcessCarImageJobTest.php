<?php

use App\Jobs\ProcessCarImageJob;
use App\Models\Cars;
use Illuminate\Support\Facades\Log;

test('job processes car image', function () {
    Log::shouldReceive('info')
        ->once()
        ->withArgs(function ($message) {
            return str_contains($message, 'Iniciando procesamiento');
        });

    Log::shouldReceive('info')
        ->once()
        ->withArgs(function ($message) {
            return str_contains($message, 'Imagen procesada');
        });

    $car = Cars::factory()->create();

    $job = new ProcessCarImageJob($car->id);
    $job->handle();
});

test('job handles missing car', function () {
    Log::shouldReceive('info')->never();

    $job = new ProcessCarImageJob(99999); // Non-existent ID
    $job->handle();
});
