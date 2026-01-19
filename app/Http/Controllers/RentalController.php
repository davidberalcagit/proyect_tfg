<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

        if ($car->id_estado !== 3) {
            return redirect()->back()->with('error', 'Este coche no está disponible para alquiler.');
        }

        $request->validate([
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        // Validar solapamiento de fechas
        $overlap = Rental::where('id_vehiculo', $car->id)
            ->whereIn('id_estado', [1, 2, 3]) // Pendiente, En espera, Usando (estados activos)
            ->where(function ($query) use ($request) {
                $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                      ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('fecha_inicio', '<=', $request->fecha_inicio)
                            ->where('fecha_fin', '>=', $request->fecha_fin);
                      });
            })
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages(['fecha_inicio' => 'El coche ya está reservado en estas fechas.']);
        }

        $days = \Carbon\Carbon::parse($request->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($request->fecha_fin));
        $totalPrice = $days * $car->precio;

        Rental::create([
            'id_vehiculo' => $car->id,
            'id_cliente' => Auth::user()->customer->id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'precio_total' => $totalPrice,
            'id_estado' => 1 // Pendiente de Aprobación
        ]);

        return redirect()->route('sales.index')->with('success', 'Solicitud de alquiler enviada. Esperando aprobación del dueño.');
    }

    public function accept(Rental $rental)
    {
        if (Auth::user()->customer->id !== $rental->car->id_vendedor) {
            abort(403);
        }

        if ($rental->car->id_estado !== 3) {
            return redirect()->back()->with('error', 'El coche ya no está disponible.');
        }

        // 2 (En espera) o 3 (Usando)
        $initialStatus = \Carbon\Carbon::parse($rental->fecha_inicio)->isToday() ? 3 : 2;

        $rental->update(['id_estado' => $initialStatus]);

        $rental->car->update(['id_estado' => 6]); // Alquilado

        // Rechazar otras pendientes que se solapen (Mejora: solo las que se solapan)
        // Por simplicidad, rechazamos todas las pendientes de este coche
        Rental::where('id_vehiculo', $rental->id_vehiculo)
            ->where('id', '!=', $rental->id)
            ->where('id_estado', 1)
            ->update(['id_estado' => 6]); // Rechazado

        return redirect()->back()->with('success', 'Alquiler aceptado.');
    }

    public function reject(Rental $rental)
    {
        if (Auth::user()->customer->id !== $rental->car->id_vendedor) {
            abort(403);
        }

        $rental->update(['id_estado' => 6]); // Rechazado

        return redirect()->back()->with('success', 'Solicitud de alquiler rechazada.');
    }
}
