<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyInactiveUsers extends Command
{
    protected $signature = 'users:inactive-notify {months=6 : Meses de inactividad}';
    protected $description = 'Notifica a usuarios inactivos.';
    public function handle()
    {
        $months = $this->argument('months');
        $date = now()->subMonths($months);
        $users = User::where('updated_at', '<', $date)->get();

        $this->info("Encontrados " . $users->count() . " usuarios inactivos desde {$date->format('Y-m-d')}.");

        foreach ($users as $user) {
            $this->line("Notificando a: {$user->email}");
            Log::info("Notificación de inactividad enviada a {$user->id}");
        }

        $this->info("Proceso completado.");
    }
}
