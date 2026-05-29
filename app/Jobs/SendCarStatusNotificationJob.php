<?php

namespace App\Jobs;

use App\Mail\CarApproved;
use App\Mail\CarRejected;
use App\Models\Cars;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCarStatusNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $car;
    public $status;
    public $reason;

    public function __construct(Cars $car, string $status, string $reason = '')
    {
        $this->car = $car;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function handle(): void
    {
        $this->car->load('vendedor.user');
        $user = $this->car->vendedor->user ?? null;

        if ($user) {
            try {
                if ($this->status === 'approved') {
                    Log::info("Iniciando Job de Aprobación para coche: {$this->car->id}");
                    Mail::to($user->email)->send(new CarApproved($this->car));
                    Log::info("Correo de aprobación enviado a: {$user->email}");
                } elseif ($this->status === 'rejected') {
                    Log::info("Iniciando Job de Rechazo para coche: {$this->car->id}");
                    Mail::to($user->email)->send(new CarRejected($this->car, $this->reason));
                    Log::info("Correo de rechazo enviado a: {$user->email}");
                }
            } catch (\Exception $e) {
                Log::error("Error en Job de estado de coche: " . $e->getMessage());
                throw $e;
            }
        } else {
            Log::warning("No se encontró usuario para el coche {$this->car->id} al notificar estado.");
        }
    }
}
