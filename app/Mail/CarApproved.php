<?php

namespace App\Mail;

use App\Models\Cars;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CarApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $car;

    public function __construct(Cars $car)
    {
        $this->car = $car;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu coche ha sido aprobado!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cars.approved',
        );
    }

    public function attachments(): array
    {
        // Generar PDF aquí
        $pdf = Pdf::loadView('pdf.certificate', ['car' => $this->car]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Certificate.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
