<?php

namespace App\Listeners;

use App\Events\CarCreated;
use App\Jobs\ProcessCarImageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogCarCreation
{
    public function handle(CarCreated $event): void
    {
        Log::info("Nuevo coche creado: {$event->car->title} (ID: {$event->car->id})");

        // También podemos mover aquí el despacho del Job de imagen
        if ($event->car->image) {
            ProcessCarImageJob::dispatch($event->car->id);
        }
    }
}
