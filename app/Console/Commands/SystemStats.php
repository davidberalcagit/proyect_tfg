<?php

namespace App\Console\Commands;

use App\Models\Cars;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Console\Command;

class SystemStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra estadísticas generales del sistema.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ESTADÍSTICAS DEL SISTEMA ===');

        $headers = ['Métrica', 'Valor'];
        $data = [
            ['Total Usuarios', User::count()],
            ['Total Coches', Cars::count()],
            ['Coches en Venta', Cars::where('id_estado', 1)->count()],
            ['Coches en Alquiler', Cars::where('id_estado', 3)->count()],
            ['Ventas Totales', Sales::count()],
            ['Ingresos Ventas', number_format(Sales::sum('precio'), 2) . ' €'],
            ['Alquileres Totales', Rental::count()],
            ['Ingresos Alquileres', number_format(Rental::sum('precio_total'), 2) . ' €'],
        ];

        $this->table($headers, $data);

        return Command::SUCCESS;
    }
}
