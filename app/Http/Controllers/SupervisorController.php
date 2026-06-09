<?php

namespace App\Http\Controllers;

use App\Events\CarRejected;
use App\Models\Cars;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_cars' => Cars::count(),
            'cars_for_sale' => Cars::where('id_estado', 1)->count(),
            'total_sales' => Sales::count(),
            'recent_sales' => Sales::latest()->take(5)->with(['vehiculo', 'vendedor'])->get(),
            'pending_cars_count' => Cars::where('id_estado', 4)->count(),
            'active_traders_count' => User::activeTraders()->count(),
        ];

        $pendingCars = Cars::where('id_estado', 4)->with(['vendedor', 'listingType'])->get();

        return view('supervisor.dashboard', compact('stats', 'pendingCars'));
    }

    public function downloadReport()
    {
        $stats = [
            'total_users' => User::count(),
            'total_cars' => Cars::count(),
            'total_sales' => Sales::count(),
            'total_rentals' => Rental::count(),
            'active_traders_count' => User::activeTraders()->count(),
        ];

        $usersByType = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as total'))
            ->whereIn('roles.name', ['individual', 'dealership'])
            ->groupBy('roles.name')
            ->get();

        $stats['users_by_type'] = $usersByType;

        $summary = Sales::getReportData();
        $stats['popular_brand'] = $summary['popular_brand'];
        $stats['sales_by_type'] = $summary['sales_by_type'];
        $topSellers = $summary['top_sellers'];

        if ($topSellers->isNotEmpty()) {
            $bestSeller = $topSellers->first();
            $stats['best_seller_monthly_sales'] = Sales::monthlyReport($bestSeller->id, now()->month, now()->year)->count();
        } else {
            $stats['best_seller_monthly_sales'] = 0;
        }

        $recentSales = Sales::latest()->take(20)->with(['vehiculo', 'vendedor', 'comprador'])->get();
        $recentRentals = Rental::latest()->take(20)->with(['car', 'customer', 'status'])->get();

        $pdf = Pdf::loadView('pdf.supervisor_report', compact('stats', 'recentSales', 'recentRentals', 'topSellers'));

        return $pdf->download('Informe_Supervisor_' . now()->format('Ymd') . '.pdf');
    }

    public function approveCar(Request $request, $id)
    {
        $exitCode = Artisan::call('cars:approve', ['car_id' => $id]);

        if ($exitCode !== 0) {
            return redirect()->back()->with('error', 'Error al aprobar el coche. Revise los logs.');
        }

        return redirect()->back()->with('success', 'Coche aprobado correctamente.');
    }

    public function rejectCar(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $car = Cars::findOrFail($id);
        $car->id_estado = 5;
        $car->rejection_reason = $request->reason;
        $car->save();

        CarRejected::dispatch($car, $request->reason);

        return redirect()->back()->with('success', 'Coche rechazado. Razón: ' . $request->reason);
    }
}
