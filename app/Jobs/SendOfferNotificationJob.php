<?php

namespace App\Jobs;

use App\Mail\NewOfferReceived;
use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOfferNotificationJob implements ShouldQueue
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
        if (!$this->offer) {
            Log::error("Offer is null in SendOfferNotificationJob");
            return;
        }

        $this->offer->load('car.vendedor.user');
        $sellerUser = $this->offer->car->vendedor->user ?? null;

        if ($sellerUser) {
            Mail::to($sellerUser->email)->send(new NewOfferReceived($this->offer));
            Log::info("Notificación de nueva oferta enviada a: {$sellerUser->email}");
        }
    }
}
