<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CleanupRejectedOffersJob;
use App\Jobs\ProcessCarImageJob;
use App\Models\Cars;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('process car image job runs', function () {
    Log::spy();
    $car = Cars::factory()->create();

    $job = new ProcessCarImageJob($car->id);
    $job->handle();

    Log::shouldHaveReceived('info')->with("Iniciando procesamiento de imagen para el coche ID: {$car->id}");
});

test('cleanup rejected offers job deletes old offers', function () {
    $oldOffer = Offer::factory()->create([
        'estado' => 'rechazada',
        'updated_at' => now()->subDays(31)
    ]);

    $recentOffer = Offer::factory()->create([
        'estado' => 'rechazada',
        'updated_at' => now()->subDays(10)
    ]);

    $job = new CleanupRejectedOffersJob();
    $job->handle();

    $this->assertDatabaseMissing('offers', ['id' => $oldOffer->id]);
    $this->assertDatabaseHas('offers', ['id' => $recentOffer->id]);
});
