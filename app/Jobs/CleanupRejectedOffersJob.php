<?php

namespace App\Jobs;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupRejectedOffersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;    public function handle(): void
    {
        Log::info("Iniciando limpieza de ofertas rechazadas antiguas...");

        $deleted = Offer::where('estado', 'rechazada')
            ->where('updated_at', '<', now()->subDays(30))
            ->delete();

        Log::info("Limpieza completada. Se eliminaron {$deleted} ofertas antiguas.");
    }
}
