<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:inactive-notify {months=6 : Meses de inactividad}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica a usuarios inactivos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = $this->argument('months');
        $date = now()->subMonths($months);

        // Asumiendo que tienes un campo last_login_at o updated_at como proxy
        // Si no tienes last_login_at, usaremos updated_at como aproximación
        $users = User::where('updated_at', '<', $date)->get();

        $this->info("Encontrados " . $users->count() . " usuarios inactivos desde {$date->format('Y-m-d')}.");

        foreach ($users as $user) {
            // Aquí enviaríamos un correo real
            // Mail::to($user)->send(new WeMissYou($user));

            $this->line("Notificando a: {$user->email}");
            Log::info("Notificación de inactividad enviada a {$user->id}");
        }

        $this->info("Proceso completado.");
    }
}
