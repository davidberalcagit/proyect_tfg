<?php

namespace App\Jobs;

use App\Mail\RentalProcessed;
use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRentalProcessedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $rental;

    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
        $this->afterCommit();
    }

    public function handle(): void
    {
        Log::info("Procesando alquiler #{$this->rental->id}");

        $customerUser = $this->rental->customer->user;
        if ($customerUser) {
            Mail::to($customerUser->email)->send(new RentalProcessed($this->rental));
        }

        $ownerUser = $this->rental->car->vendedor->user;
        if ($ownerUser) {
            Mail::to($ownerUser->email)->send(new RentalProcessed($this->rental));
        }
    }
}
