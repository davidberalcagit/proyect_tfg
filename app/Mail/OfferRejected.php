<?php

namespace App\Mail;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu oferta ha sido rechazada',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.offers.rejected',
        );
    }
}
