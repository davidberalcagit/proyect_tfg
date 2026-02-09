<?php

namespace App\Mail;

use App\Models\Rental;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalProcessed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $rental;
    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alquiler Procesado - Recibo Adjunto',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rentals.processed',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.rental_receipt', ['rental' => $this->rental]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Recibo_Alquiler.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
