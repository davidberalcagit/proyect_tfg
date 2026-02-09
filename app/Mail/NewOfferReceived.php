<?php

namespace App\Mail;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOfferReceived extends Mailable
{
    use Queueable, SerializesModels;
        public function __construct(public Offer $offer)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Offer Received for ' . $this->offer->car->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.offers.received',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
