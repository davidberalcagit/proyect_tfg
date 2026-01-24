<?php

namespace App\Console\Commands;

use App\Jobs\SendCarApprovedNotificationJob;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApproveCar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:approve {car_id : El ID del coche a aprobar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aprueba un coche pendiente de revisión, procesando marcas/modelos temporales.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $carId = $this->argument('car_id');
        $car = Cars::find($carId);

        if (!$car) {
            $this->error("Coche con ID {$carId} no encontrado.");
            return Command::FAILURE;
        }

        try {
            DB::transaction(function () use ($car) {
                // Procesar Marca Temporal
                if ($car->temp_brand) {
                    $brand = Brands::firstOrCreate(['nombre' => $car->temp_brand]);
                    $car->id_marca = $brand->id;
                    $car->temp_brand = null;
                    $this->info("Marca temporal '{$brand->nombre}' procesada.");
                }

                // Procesar Modelo Temporal
                if ($car->temp_model) {
                    if (!$car->id_marca) {
                        throw new \Exception("Error: Marca no definida para el modelo temporal.");
                    }

                    $model = CarModels::firstOrCreate([
                        'nombre' => $car->temp_model,
                        'id_marca' => $car->id_marca
                    ]);
                    $car->id_modelo = $model->id;
                    $car->temp_model = null;
                    $this->info("Modelo temporal '{$model->nombre}' procesado.");
                }

                // Procesar Color Temporal
                if ($car->temp_color) {
                    $color = Color::firstOrCreate(['nombre' => $car->temp_color]);
                    $car->id_color = $color->id;
                    $car->temp_color = null;
                    $this->info("Color temporal '{$color->nombre}' procesado.");
                }

                // Determinar estado final
                if ($car->listingType && $car->listingType->nombre === 'Alquiler') {
                    $car->id_estado = 3; // En Alquiler
                } else {
                    $car->id_estado = 1; // En Venta
                }

                $car->save();
            });

            // Enviar notificación
            SendCarApprovedNotificationJob::dispatch($car);

            $this->info("Coche ID {$carId} aprobado correctamente (Estado: {$car->id_estado}).");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error al aprobar coche: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
