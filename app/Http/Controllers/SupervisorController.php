<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Http\Request;
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
        ];

        $pendingCars = Cars::where('id_estado', 4)->with(['vendedor'])->get();

        return view('supervisor.dashboard', compact('stats', 'pendingCars'));
    }

    public function approveCar(Request $request, $id)
    {
        $car = Cars::findOrFail($id);

        DB::transaction(function () use ($car) {
            // 1. Marca
            if ($car->temp_brand) {
                $brand = Brands::firstOrCreate(['nombre' => $car->temp_brand]);
                $car->id_marca = $brand->id;
                $car->temp_brand = null;
            }

            // 2. Modelo
            if ($car->temp_model) {
                if (!$car->id_marca) throw new \Exception("Error: Marca no definida.");

                $model = CarModels::firstOrCreate([
                    'nombre' => $car->temp_model,
                    'id_marca' => $car->id_marca
                ]);
                $car->id_modelo = $model->id;
                $car->temp_model = null;
            }

            // 3. Color (Nuevo)
            if ($car->temp_color) {
                $color = Color::firstOrCreate(['nombre' => $car->temp_color]);
                $car->id_color = $color->id;
                $car->temp_color = null;
            }

            // 4. Aprobar
            $car->id_estado = 1;
            $car->save();
        });

        return redirect()->back()->with('success', 'Coche aprobado y atributos creados correctamente.');
    }

    public function rejectCar($id)
    {
        $car = Cars::findOrFail($id);
        $car->id_estado = 5; // Rechazado
        $car->save();

        return redirect()->back()->with('success', 'Coche rechazado.');
    }
}
