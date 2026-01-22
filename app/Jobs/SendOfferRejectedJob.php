<?php

namespace App\Jobs;

use App\Mail\OfferRejected;
use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOfferRejectedJob implements ShouldQueue
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
        $buyerUser = $this->offer->buyer->user;
        if ($buyerUser) {
            Mail::to($buyerUser->email)->send(new OfferRejected($this->offer));
        }
    }
}
