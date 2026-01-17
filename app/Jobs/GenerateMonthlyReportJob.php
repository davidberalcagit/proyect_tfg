<?php

namespace App\Jobs;

use App\Models\Dealerships;
use App\Models\Sales;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateMonthlyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dealershipId;

    /**
     * Create a new job instance.
     */
    public function __construct($dealershipId)
    {
        $this->dealershipId = $dealershipId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando generación de reporte mensual para Concesionario ID: {$this->dealershipId}");

        // Simular cálculo complejo
        sleep(3);

        $sales = Sales::where('id_vendedor', $this->dealershipId) // Asumiendo que id_vendedor apunta al cliente del concesionario
            ->whereMonth('created_at', now()->month)
            ->get();

        $total = $sales->sum('precio');
        $count = $sales->count();

        $reportContent = "Reporte Mensual - " . now()->format('F Y') . "\n";
        $reportContent .= "Concesionario ID: {$this->dealershipId}\n";
        $reportContent .= "Ventas Totales: {$count}\n";
        $reportContent .= "Ingresos Totales: {$total} €\n";

        // Guardar en disco
        $fileName = "reports/monthly_{$this->dealershipId}_" . now()->format('Y_m') . ".txt";
        Storage::disk('local')->put($fileName, $reportContent);

        Log::info("Reporte generado y guardado en: {$fileName}");
    }
}
