<?php

namespace App\Jobs;

use App\Models\Cars;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AuditCarPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando auditoría de precios de coches...");

        $cars = Cars::where('precio', '<=', 0)->get();

        foreach ($cars as $car) {
            Log::warning("Coche ID {$car->id} tiene un precio sospechoso: {$car->precio} €. Marcando para revisión.");

            // Asumiendo que tienes un estado '4' para 'Revisión' o similar
            // $car->update(['id_estado' => 4]);
        }

        if ($cars->isEmpty()) {
            Log::info("Auditoría finalizada. Todos los precios parecen correctos.");
        } else {
            Log::info("Auditoría finalizada. Se encontraron {$cars->count()} coches con precios incorrectos.");
        }
    }
}
