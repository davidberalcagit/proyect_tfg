<?php

namespace App\Jobs;

use App\Mail\RentalReturnReminder;
use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRentalReturnReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $rental;

    /**
     * Create a new job instance.
     */
    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->rental->customer->user;

        if ($user) {
            try {
                Mail::to($user->email)->send(new RentalReturnReminder($this->rental));
                Log::info("Recordatorio de devolución enviado al cliente {$user->email} para el alquiler #{$this->rental->id}");
            } catch (\Exception $e) {
                Log::error("Error al enviar recordatorio de devolución: " . $e->getMessage());
                throw $e; // Reintentar el job si falla
            }
        } else {
            Log::warning("No se encontró usuario para el cliente del alquiler #{$this->rental->id}");
        }
    }
}
