<?php

namespace App\Jobs;

use App\Mail\SaleProcessed;
use App\Models\Sales;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSaleProcessedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $sale;

    public function __construct(Sales $sale)
    {
        $this->sale = $sale;
        $this->afterCommit();
    }
    public function handle(): void
    {
        Log::info("Procesando venta #{$this->sale->id}");

        $buyerUser = $this->sale->comprador->user;
        if ($buyerUser) {
            Mail::to($buyerUser->email)->send(new SaleProcessed($this->sale));
            Log::info("Recibo enviado al comprador: {$buyerUser->email}");
        }

        $sellerUser = $this->sale->vendedor->user;
        if ($sellerUser) {
            Mail::to($sellerUser->email)->send(new SaleProcessed($this->sale));
            Log::info("Recibo enviado al vendedor: {$sellerUser->email}");
        }
    }
}
