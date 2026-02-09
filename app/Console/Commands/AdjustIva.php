<?php

namespace App\Console\Commands;

use App\Models\Cars;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdjustIva extends Command
{
    protected $signature = 'prices:adjust-iva
                            {action : Acción a realizar (give: sumar IVA, remove: quitar IVA)}
                            {target : Objetivo (individual, dealership, o ID del coche)}';

    protected $description = 'Suma o resta el IVA (21%) al precio de los coches.';

    public function handle()
    {
        $action = $this->argument('action');
        $target = $this->argument('target');

        if (!in_array($action, ['give', 'remove'])) {
            $this->error('La acción debe ser "give" (sumar IVA) o "remove" (quitar IVA).');
            return Command::FAILURE;
        }

        $query = Cars::where('id_estado', 1);

        if (is_numeric($target)) {
            $query->where('id', $target);
            $this->info("Aplicando acción '{$action}' al coche ID: {$target}");
        } elseif (in_array($target, ['individual', 'dealership'])) {
            $query->whereHas('vendedor.user.roles', function ($q) use ($target) {
                $q->where('name', $target);
            });
            $this->info("Aplicando acción '{$action}' a vendedores tipo: {$target}");
        } else {
            $this->error('El objetivo debe ser "individual", "dealership" o un ID numérico.');
            return Command::FAILURE;
        }

        $carsToUpdate = $query->get();

        if ($carsToUpdate->isEmpty()) {
            $this->warn("No se encontraron coches que coincidan con los criterios.");
            return Command::SUCCESS;
        }

        $count = 0;

        DB::transaction(function () use ($carsToUpdate, $action, &$count) {
            foreach ($carsToUpdate as $car) {
                $oldPrice = $car->precio;

                if ($action === 'give') {
                    $newPrice = $oldPrice * 1.21;
                } else {
                    $newPrice = $oldPrice / 1.21;
                }

                $car->update(['precio' => $newPrice]);
                $this->line("Coche #{$car->id}: {$oldPrice}€ -> {$newPrice}€");
                $count++;
            }
        });

        $this->info("Proceso finalizado. Se actualizaron {$count} coches.");
        return Command::SUCCESS;
    }
}
