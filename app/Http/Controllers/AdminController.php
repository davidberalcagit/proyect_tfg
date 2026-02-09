<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessCarImageJob;
use App\Jobs\SendOfferNotificationJob;
use App\Jobs\SendWelcomeEmailJob;
use App\Jobs\CleanupRejectedOffersJob;
use App\Jobs\AuditCarPricesJob;
use App\Models\Cars;
use App\Models\Offer;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function runJob(Request $request)
    {
        $job = $request->input('job');
        $output = '';

        try {
            switch ($job) {
                case 'clear-cache':
                    Artisan::call('cache:clear');
                    $output = Artisan::output();
                    break;
                case 'optimize':
                    Artisan::call('optimize:clear');
                    $output = Artisan::output();
                    break;
                case 'queue-work':
                    Artisan::call('queue:work', ['--stop-when-empty' => true]);
                    $output = Artisan::output();
                    if (empty($output)) $output = "Cola procesada (o estaba vacía).";
                    break;

                case 'check-rentals':
                    Artisan::call('rentals:process-daily');
                    $output = Artisan::output();
                    if (empty($output)) $output = "Procesamiento de alquileres completado.";
                    break;

                case 'cleanup-images':
                    Artisan::call('cars:cleanup-images');
                    $output = Artisan::output();
                    if (empty($output)) $output = "Limpieza de imágenes completada.";
                    break;

                case 'process-image':
                    $car = Cars::first();
                    if ($car) {
                        ProcessCarImageJob::dispatch($car->id);
                        $output = "Job 'ProcessCarImageJob' enviado a la cola para el coche ID: {$car->id}";
                    } else {
                        $output = "No hay coches para procesar.";
                    }
                    break;

                case 'cleanup-offers':
                    CleanupRejectedOffersJob::dispatch();
                    $output = "Job 'CleanupRejectedOffersJob' ejecutado correctamente (Síncrono).";
                    break;

                case 'audit-prices':
                    AuditCarPricesJob::dispatch();
                    $output = "Job 'AuditCarPricesJob' ejecutado correctamente (Síncrono).";
                    break;

                default:
                    return redirect()->back()->with('error', 'Job no reconocido.');
            }

            return redirect()->back()->with('success', 'Acción iniciada exitosamente.')->with('output', $output);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al ejecutar: ' . $e->getMessage());
        }
    }
}
