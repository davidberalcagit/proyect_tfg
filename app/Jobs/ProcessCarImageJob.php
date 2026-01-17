<?php

namespace App\Jobs;

use App\Models\Cars;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCarImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $carId;

    /**
     * Create a new job instance.
     */
    public function __construct($carId)
    {
        $this->carId = $carId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $car = Cars::find($this->carId);

        if (!$car) {
            return;
        }

        Log::info("Iniciando procesamiento de imagen para el coche ID: {$this->carId}");

        // Simulación de proceso pesado (Redimensionar, Marca de agua)
        // En un caso real usarías 'Intervention Image'
        sleep(2); // Simula 2 segundos de procesamiento

        // Aquí actualizarías la ruta de la imagen si generaste una miniatura
        // $car->update(['image_thumbnail' => 'path/to/thumb.jpg']);

        Log::info("Imagen procesada y marca de agua aplicada para el coche ID: {$this->carId}");
    }
}
