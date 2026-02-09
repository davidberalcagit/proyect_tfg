<?php

namespace App\Console\Commands;

use App\Jobs\SendRentalReturnReminderJob;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueRentals extends Command
{
    protected $signature = 'rentals:process-daily';
    protected $description = 'Procesa el ciclo de vida diario de los alquileres (activar, finalizar, expirar y notificar).';
    public function handle()
    {
        $today = Carbon::today();
        $this->info("Iniciando procesamiento diario de alquileres para: {$today->format('d/m/Y')}");

        $startingRentals = Rental::where('id_estado', 2)
            ->whereDate('fecha_inicio', '<=', $today)
            ->whereDate('fecha_fin', '>', $today)
            ->get();

        foreach ($startingRentals as $rental) {
            $rental->update(['id_estado' => 3]);
            $this->line("Alquiler #{$rental->id} iniciado (Usando).");
            Log::info("Alquiler {$rental->id} iniciado (Usando).");
        }

        $endingRentals = Rental::where('id_estado', 3)
            ->whereDate('fecha_fin', $today)
            ->get();

        foreach ($endingRentals as $rental) {
            $rental->update(['id_estado' => 2]);

            SendRentalReturnReminderJob::dispatch($rental);

            $this->info("Alquiler #{$rental->id} finaliza hoy. Recordatorio enviado.");
            Log::info("Alquiler {$rental->id} marcado para devolución hoy. Job de recordatorio despachado.");
        }

        $expiredRentals = Rental::where('id_estado', 2)
            ->whereDate('fecha_fin', '<', $today)
            ->get();

        foreach ($expiredRentals as $rental) {
            $rental->update(['id_estado' => 4]);
            $this->error("Alquiler #{$rental->id} ha expirado.");
            Log::warning("Alquiler {$rental->id} expirado.");
        }

        $this->info("Procesamiento diario completado.");
    }
}
