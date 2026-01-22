<?php

namespace App\Console\Commands;

use App\Events\OfferRejected;
use App\Jobs\SendOfferRejectedJob;
use App\Models\Offer;
use Illuminate\Console\Command;

class AutoRejectLowOffers extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'offers:auto-reject-low {--percentage=50 (defecto 50)}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Rechaza automáticamente ofertas pendientes que sean demasiado bajas respecto al precio del coche.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $percentage = $this->option('percentage');
        $this->info("Buscando ofertas pendientes inferiores al {$percentage}% del precio del vehículo...");

        $offers = Offer::where('estado', 'pending')->with('car')->get();
        $count = 0;

        foreach ($offers as $offer) {
            if (!$offer->car) continue;

            $minPrice = $offer->car->precio * ($percentage / 100);
            if ($offer->cantidad < $minPrice) {
                $offer->update(['estado' => 'rejected']);
                // Notificar al comprador
                SendOfferRejectedJob::dispatch($offer);
                $this->line("Oferta #{$offer->id} rechazada: {$offer->cantidad}€ (Precio coche: {$offer->car->precio}€)");
                $count++;
            }
        }
        $this->info("Se han rechazado {$count} ofertas.");
    }
}
