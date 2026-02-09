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
    public function __construct($carId)
    {
        $this->carId = $carId;
    }


    public function handle(): void
    {
        $car = Cars::find($this->carId);

        if (!$car) {
            return;
        }

        Log::info("Iniciando procesamiento de imagen para el coche ID: {$this->carId}");

        sleep(2);
        Log::info("Imagen procesada y marca de agua aplicada para el coche ID: {$this->carId}");
    }
}
