<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    public function create(Cars $car)
    {
        return view('rentals.create', compact('car'));
    }

    public function store(Request $request, Cars $car)
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión y tener perfil de cliente.');
        }

        // Verificar que el coche esté disponible para alquiler (id_estado = 3)
        if ($car->id_estado !== 3) {
            return redirect()->back()->with('error', 'Este coche no está disponible para alquiler.');
        }

        $request->validate([
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        // Calcular precio total (días * precio diario)
        // Asumimos que $car->precio es el precio por día para alquileres
        $days = \Carbon\Carbon::parse($request->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($request->fecha_fin));
        $totalPrice = $days * $car->precio;

        Rental::create([
            'id_vehiculo' => $car->id,
            'id_cliente' => Auth::user()->customer->id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'precio_total' => $totalPrice,
            'estado' => 'active'
        ]);

        // Cambiar estado del coche a "Alquilado" (6)
        $car->update(['id_estado' => 6]);

        return redirect()->route('cars.show', $car)->with('success', 'Alquiler realizado con éxito.');
    }
}
