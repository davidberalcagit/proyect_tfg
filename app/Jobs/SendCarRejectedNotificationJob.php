<?php

namespace App\Jobs;

use App\Mail\CarRejected;
use App\Models\Cars;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCarRejectedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $car;
    public $reason;

    public function __construct(Cars $car, string $reason)
    {
        $this->car = $car;
        $this->reason = $reason;
    }

    public function handle(): void
    {
        Log::info("Iniciando Job de Rechazo para coche: {$this->car->id}");
        $this->car->load('vendedor.user');

        $user = $this->car->vendedor->user ?? null;

        if ($user) {
            try {
                Mail::to($user->email)->send(new CarRejected($this->car, $this->reason));
                Log::info("Correo de rechazo enviado a: {$user->email}");
            } catch (\Exception $e) {
                Log::error("Error enviando correo de rechazo: " . $e->getMessage());
                throw $e;
            }
        } else {
            Log::warning("No se encontró usuario vendedor para el coche {$this->car->id} al rechazar.");
        }
    }
}
