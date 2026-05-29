<?php

namespace App\Listeners;

use App\Events\SaleCompleted;
use App\Jobs\SendSaleProcessedJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifySaleParticipants
{
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;

        Log::info("Venta completada: Coche ID {$sale->id_vehiculo} vendido por Usuario ID {$sale->id_vendedor} a Usuario ID {$sale->id_comprador} por {$sale->precio}€.");

        SendSaleProcessedJob::dispatch($event->sale);
    }
}
