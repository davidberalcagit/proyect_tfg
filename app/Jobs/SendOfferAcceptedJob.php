<?php

namespace App\Jobs;

use App\Mail\OfferAccepted;
use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOfferAcceptedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
        $this->afterCommit();
    }

    public function handle(): void
    {
        // Enviar correo SOLO al comprador
        $buyerUser = $this->offer->buyer->user;

        if ($buyerUser) {
            Log::info("Enviando correo de aceptación de oferta al comprador: {$buyerUser->email}");
            Mail::to($buyerUser->email)->send(new OfferAccepted($this->offer));
        } else {
            Log::error("No se encontró usuario comprador para la oferta {$this->offer->id}");
        }
    }
}
