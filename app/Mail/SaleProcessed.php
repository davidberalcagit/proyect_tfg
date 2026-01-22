<?php

namespace App\Mail;

use App\Models\Sales;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SaleProcessed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $sale;

    public function __construct(Sales $sale)
    {
        $this->sale = $sale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Venta Procesada - Recibo Adjunto',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sales.processed',
        );
    }

    public function attachments(): array
    {
        // Generar PDF dentro del Mailable
        $pdf = Pdf::loadView('pdf.sale_receipt', ['sale' => $this->sale]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Recibo_Venta.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
