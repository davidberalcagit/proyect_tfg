<?php

use App\Jobs\AuditCarPricesJob;
use App\Models\Cars;
use Illuminate\Support\Facades\Log;

test('audit job logs suspicious prices', function () {
    Log::shouldReceive('info')->atLeast()->once();
    Log::shouldReceive('warning')->once()->withArgs(fn($msg) => str_contains($msg, 'precio sospechoso'));

    Cars::factory()->create(['precio' => 0]);
    Cars::factory()->create(['precio' => 10000]);

    $job = new AuditCarPricesJob();
    $job->handle();
});

test('audit job logs all correct', function () {
    Log::shouldReceive('info')->atLeast()->once();
    Log::shouldReceive('warning')->never();

    Cars::factory()->create(['precio' => 10000]);

    $job = new AuditCarPricesJob();
    $job->handle();
});
