<?php

namespace App\Jobs;

use App\Mail\CarApproved;
use App\Models\Cars;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCarApprovedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $car;

    public function __construct(Cars $car)
    {
        $this->car = $car;
    }

    public function handle(): void
    {
        Log::info("Iniciando Job de Aprobación para coche: {$this->car->id}");

        $user = $this->car->vendedor->user;

        if ($user) {
            try {
                Mail::to($user->email)->send(new CarApproved($this->car));
                Log::info("Correo enviado a: {$user->email}");
            } catch (\Exception $e) {
                Log::error("Error en Job de Aprobación: " . $e->getMessage());
                throw $e;
            }
        } else {
            Log::warning("No se encontró usuario para el vendedor del coche {$this->car->id}");
        }
    }
}
