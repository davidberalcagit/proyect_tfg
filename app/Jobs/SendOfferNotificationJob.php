<?php

namespace App\Jobs;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
// Asegúrate de tener un Mailable creado, si no, usaremos Log para simular
// use App\Mail\OfferReceived;

class SendOfferNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $offer;

    /**
     * Create a new job instance.
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Enviando notificación de oferta ID: {$this->offer->id} al vendedor...");

        // Simulación de envío de correo (puede tardar 1-3 segundos)
        sleep(1);

        // Código real de envío de correo:
        // Mail::to($this->offer->seller->user->email)->send(new OfferReceived($this->offer));

        Log::info("Correo de oferta enviado exitosamente a: " . $this->offer->seller->user->email);
    }
}
