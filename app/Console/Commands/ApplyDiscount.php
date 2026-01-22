<?php

namespace App\Console\Commands;

use App\Models\Brands;
use App\Models\Cars;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApplyDiscount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prices:modify
                            {percentage : Porcentaje a aplicar (1-100)}
                            {target : Objetivo (individual, dealership, all, ID numérico o Nombre de Marca)}
                            {mode=decrease : Modo de operación (decrease: bajar precio, increase: subir precio)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modifica masivamente el precio de los coches (descuento o aumento).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $percentage = $this->argument('percentage');
        $target = $this->argument('target');
        $mode = $this->argument('mode');

        if (!is_numeric($percentage) || $percentage <= 0) {
            $this->error('El porcentaje debe ser un número positivo.');
            return Command::FAILURE;
        }

        if (!in_array($mode, ['increase', 'decrease'])) {
            $this->error('El modo debe ser "increase" (subir) o "decrease" (bajar).');
            return Command::FAILURE;
        }

        $actionText = $mode === 'increase' ? 'Aumentando' : 'Descontando';
        $this->info("{$actionText} un {$percentage}%...");

        $query = Cars::where('id_estado', 1); // Solo coches en venta

        if (is_numeric($target)) {
            $query->where('id', $target);
            $this->info("Objetivo: Coche ID {$target}");
        } elseif ($target === 'all') {
            $this->info("Objetivo: Todos los coches en venta");
        } elseif (in_array($target, ['individual', 'dealership'])) {
            $query->whereHas('vendedor.user.roles', function ($q) use ($target) {
                $q->where('name', $target);
            });
            $this->info("Objetivo: Vendedores tipo {$target}");
        } else {
            $brand = Brands::where('nombre', $target)->first();
            if ($brand) {
                $query->where('id_marca', $brand->id);
                $this->info("Objetivo: Marca {$brand->nombre}");
            } else {
                $this->error("El objetivo '{$target}' no es válido.");
                return Command::FAILURE;
            }
        }

        $carsToUpdate = $query->get();

        if ($carsToUpdate->isEmpty()) {
            $this->warn("No se encontraron coches.");
            return Command::SUCCESS;
        }

        $count = 0;

        DB::transaction(function () use ($carsToUpdate, $percentage, $mode, &$count) {
            foreach ($carsToUpdate as $car) {
                $oldPrice = $car->precio;

                if ($mode === 'increase') {
                    $newPrice = $oldPrice * (1 + ($percentage / 100));
                } else {
                    $newPrice = $oldPrice * (1 - ($percentage / 100));
                }

                $car->update(['precio' => $newPrice]);
                $this->line("Coche #{$car->id}: {$oldPrice}€ -> {$newPrice}€");
                $count++;
            }
        });

        $this->info("Proceso finalizado. {$count} coches actualizados.");
        return Command::SUCCESS;
    }
}
