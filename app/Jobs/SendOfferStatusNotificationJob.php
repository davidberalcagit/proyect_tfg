<?php

namespace App\Jobs;

use App\Mail\OfferAccepted;
use App\Mail\OfferRejected;
use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOfferStatusNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $offer;
    public $status;

    public function __construct(Offer $offer, string $status)
    {
        $this->offer = $offer;
        $this->status = $status;
        $this->afterCommit();
    }

    public function handle(): void
    {
        $buyerUser = $this->offer->buyer->user;

        if ($buyerUser) {
            if ($this->status === 'accepted') {
                Log::info("Enviando correo de aceptación de oferta al comprador: {$buyerUser->email}");
                Mail::to($buyerUser->email)->send(new OfferAccepted($this->offer));
            } elseif ($this->status === 'rejected') {
                Log::info("Enviando correo de rechazo de oferta al comprador: {$buyerUser->email}");
                Mail::to($buyerUser->email)->send(new OfferRejected($this->offer));
            }
        } else {
            Log::error("No se encontró usuario comprador para la oferta {$this->offer->id}");
        }
    }
}
