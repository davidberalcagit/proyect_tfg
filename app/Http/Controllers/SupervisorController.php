<?php

namespace App\Http\Controllers;

use App\Events\CarRejected; // Importar Evento
use App\Jobs\SendCarApprovedNotificationJob;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        ];

        $usersByType = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as total'))
            ->whereIn('roles.name', ['individual', 'dealership'])
            ->groupBy('roles.name')
            ->get();

        $stats['users_by_type'] = $usersByType;

        $popularBrand = Sales::join('cars', 'sales.id_vehiculo', '=', 'cars.id')
            ->join('brands', 'cars.id_marca', '=', 'brands.id')
            ->select('brands.nombre', DB::raw('count(*) as total'))
            ->groupBy('brands.nombre')
            ->orderByDesc('total')
            ->first();

        $stats['popular_brand'] = $popularBrand ? $popularBrand->nombre . ' (' . $popularBrand->total . ')' : 'N/A';

        $salesByType = Sales::join('customers', 'sales.id_vendedor', '=', 'customers.id')
            ->join('entity_types', 'customers.id_entidad', '=', 'entity_types.id')
            ->select('entity_types.nombre', DB::raw('count(*) as total'))
            ->groupBy('entity_types.nombre')
            ->get();

        $stats['sales_by_type'] = $salesByType;

        $topSellers = Sales::join('customers', 'sales.id_vendedor', '=', 'customers.id')
            ->select('customers.nombre_contacto', DB::raw('count(*) as total_sales'), DB::raw('sum(sales.precio) as total_revenue'))
            ->groupBy('customers.id', 'customers.nombre_contacto')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        $recentSales = Sales::latest()->take(20)->with(['vehiculo', 'vendedor', 'comprador'])->get();
        $recentRentals = Rental::latest()->take(20)->with(['car', 'customer', 'status'])->get();

        $pdf = Pdf::loadView('pdf.supervisor_report', compact('stats', 'recentSales', 'recentRentals', 'topSellers'));

        return $pdf->download('Informe_Supervisor_' . now()->format('Ymd') . '.pdf');
    }

    public function approveCar(Request $request, $id)
    {
        $car = Cars::findOrFail($id);

        DB::transaction(function () use ($car) {
            if ($car->temp_brand) {
                $brand = Brands::firstOrCreate(['nombre' => $car->temp_brand]);
                $car->id_marca = $brand->id;
                $car->temp_brand = null;
            }

            if ($car->temp_model) {
                if (!$car->id_marca) throw new \Exception("Error: Marca no definida.");

                $model = CarModels::firstOrCreate([
                    'nombre' => $car->temp_model,
                    'id_marca' => $car->id_marca
                ]);
                $car->id_modelo = $model->id;
                $car->temp_model = null;
            }

            if ($car->temp_color) {
                $color = Color::firstOrCreate(['nombre' => $car->temp_color]);
                $car->id_color = $color->id;
                $car->temp_color = null;
            }

            if ($car->listingType && $car->listingType->nombre === 'Alquiler') {
                $car->id_estado = 3;
            } else {
                $car->id_estado = 1;
            }

            $car->save();
        });

        SendCarApprovedNotificationJob::dispatch($car);

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

        // Disparar evento en lugar de Job directo
        CarRejected::dispatch($car, $request->reason);

        return redirect()->back()->with('success', 'Coche rechazado. Razón: ' . $request->reason);
    }
}
