<?php

namespace App\Console\Commands;

use App\Models\Sales;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:export {user_id : El ID del usuario vendedor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporta el historial de ventas de un usuario a un archivo CSV.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user || !$user->customer) {
            $this->error("Usuario o perfil de cliente no encontrado.");
            return Command::FAILURE;
        }

        $sales = Sales::where('id_vendedor', $user->customer->id)->with('vehiculo', 'comprador')->get();

        if ($sales->isEmpty()) {
            $this->warn("No hay ventas para este usuario.");
            return Command::SUCCESS;
        }

        $fileName = "sales_export_{$userId}_" . now()->format('Ymd_His') . ".csv";
        $filePath = "exports/{$fileName}";

        // Crear contenido CSV
        $csvData = [];
        $csvData[] = ['ID Venta', 'Coche', 'Comprador', 'Precio', 'Fecha'];

        foreach ($sales as $sale) {
            $csvData[] = [
                $sale->id,
                $sale->vehiculo->title,
                $sale->comprador->nombre_contacto,
                $sale->precio,
                $sale->created_at->format('Y-m-d H:i:s'),
            ];
        }

        // Escribir archivo
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        Storage::disk('public')->put($filePath, $content);

        $this->info("Exportación completada: {$filePath}");

        // Devolver la ruta relativa para que el controlador pueda usarla
        // Artisan::output() captura lo que escribimos con info/line, pero es mejor si el controlador sabe dónde buscar.
        // Por convención, el controlador buscará el último archivo generado o pasaremos el nombre de alguna forma si fuera un job.
        // Aquí solo informamos.

        return Command::SUCCESS;
    }
}
