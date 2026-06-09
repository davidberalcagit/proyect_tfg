<?php

namespace App\Mail;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferAccepted extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(public Offer $offer)
    {
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu oferta ha sido aceptada!',
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.offers.accepted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
