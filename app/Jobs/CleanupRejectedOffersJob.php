<?php

namespace App\Jobs;

use App\Models\Offer;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CleanupRejectedOffersJob
{
    use Dispatchable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando limpieza de ofertas rechazadas antiguas...");

        // Eliminar ofertas rechazadas de hace más de 30 días
        $deleted = Offer::where('estado', 'rechazada')
            ->where('updated_at', '<', now()->subDays(30))
            ->delete();

        Log::info("Limpieza completada. Se eliminaron {$deleted} ofertas antiguas.");
    }
}
