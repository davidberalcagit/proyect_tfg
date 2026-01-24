<?php

namespace App\Listeners;

use App\Events\SaleCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogSaleActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;

        Log::info("Venta completada: Coche ID {$sale->id_vehiculo} vendido por Usuario ID {$sale->id_vendedor} a Usuario ID {$sale->id_comprador} por {$sale->precio}€.");
    }
}
