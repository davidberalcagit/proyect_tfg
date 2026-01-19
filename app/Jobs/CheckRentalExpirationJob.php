<?php

namespace App\Jobs;

use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckRentalExpirationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = Carbon::today();

        // 1. Activar alquileres que empiezan hoy (de 'En espera' (2) a 'Usando' (3))
        $startingRentals = Rental::where('id_estado', 2)
            ->whereDate('fecha_inicio', '<=', $today)
            ->get();

        foreach ($startingRentals as $rental) {
            $rental->update(['id_estado' => 3]);
            Log::info("Alquiler {$rental->id} iniciado.");
        }

        // 2. Expirar alquileres que terminaron ayer (de 'Usando' (3) a 'Fecha expirada' (4))
        $expiredRentals = Rental::where('id_estado', 3)
            ->whereDate('fecha_fin', '<', $today)
            ->get();

        foreach ($expiredRentals as $rental) {
            $rental->update(['id_estado' => 4]);
            Log::info("Alquiler {$rental->id} expirado.");
        }
    }
}
