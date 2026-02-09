<?php

namespace App\Mail;

use App\Models\Cars;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CarRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $car;
    public $reason;

    public function __construct(Cars $car, string $reason)
    {
        $this->car = $car;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu coche ha sido rechazado',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cars.rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
