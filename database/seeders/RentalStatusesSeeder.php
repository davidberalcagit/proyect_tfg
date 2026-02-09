<?php

namespace Database\Seeders;

use App\Models\RentalStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentalStatusesSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            1 => 'Pendiente de Aprobación',
            2 => 'En espera de entrega',
            3 => 'Usando',
            4 => 'Fecha expirada',
            5 => 'Devuelto/Completado',
            6 => 'Rechazado',
            7 => 'Aceptado por dueño (Esperando pago)'
        ];

        foreach ($statuses as $id => $nombre) {
            RentalStatus::updateOrCreate(['id' => $id], ['nombre' => $nombre]);
        }
    }
}
