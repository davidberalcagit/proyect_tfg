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
        // 1: En venta, 2: Vendido, 3: En alquiler, 4: Alquilado
        $carStatuses = ['En venta', 'Vendido', 'En alquiler', 'Alquilado'];
        foreach ($carStatuses as $status) {
            CarStatus::firstOrCreate(['nombre' => $status]);
        }

        // Sale Statuses
        $saleStatuses = ['Completada', 'Pendiente', 'Cancelada'];
        foreach ($saleStatuses as $status) {
            SaleStatus::firstOrCreate(['nombre' => $status]);
        }
    }
}
