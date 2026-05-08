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
        $days = $this->rental->fecha_inicio->diffInDays($this->rental->fecha_fin);
        if ($days == 0) $days = 1;

        $total = $this->rental->precio_total;
        $serviceFee = $total * 0.05;
        $tax = $serviceFee * 0.21;
        $grandTotal = $total + $serviceFee + $tax;

        $pdf = Pdf::loadView('pdf.rental_receipt', [
            'rental' => $this->rental,
            'days' => $days,
            'total' => $total,
            'serviceFee' => $serviceFee,
            'tax' => $tax,
            'grandTotal' => $grandTotal
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Recibo_Alquiler.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
