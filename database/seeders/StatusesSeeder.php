<?php

namespace Database\Seeders;

use App\Models\CarStatus;
use App\Models\SaleStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusesSeeder extends Seeder
{
    public function run(): void
    {
        // Car Statuses
        // 1: En Venta
        // 2: Vendido
        // 3: En Alquiler
        // 4: Pendiente de Revisión
        // 5: Rechazado
        // 6: Alquilado

        $carStatuses = [
            1 => 'En Venta',
            2 => 'Vendido',
            3 => 'En Alquiler',
            4 => 'Pendiente de Revisión',
            5 => 'Rechazado',
            6 => 'Alquilado'
        ];

        foreach ($carStatuses as $id => $nombre) {
            CarStatus::updateOrCreate(['id' => $id], ['nombre' => $nombre]);
        }

        // Sale Statuses
        $saleStatuses = ['Completada', 'Pendiente', 'Cancelada'];
        foreach ($saleStatuses as $status) {
            SaleStatus::firstOrCreate(['nombre' => $status]);
        }
    }
}
