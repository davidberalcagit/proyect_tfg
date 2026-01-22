<?php

namespace App\Http\Controllers;

use App\Events\RentalPaid; // Importar Evento
use App\Mail\NewRentalRequest;
use App\Mail\RentalAccepted;
use App\Mail\RentalRejected;
use App\Models\Cars;
use App\Models\Rental;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RentalController extends Controller
{
    public function create(Cars $car)
    {
        return view('rentals.create', compact('car'));
    }

    public function downloadTerms()
    {
        $pdf = Pdf::loadView('pdf.rental_terms');
        return $pdf->stream('Terminos_y_Condiciones_Alquiler.pdf');
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
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $overlap = Rental::overlapping($car->id, $request->fecha_inicio, $request->fecha_fin)->exists();

        if ($overlap) {
            throw ValidationException::withMessages(['fecha_inicio' => 'El coche ya está reservado en estas fechas.']);
        }

        $days = \Carbon\Carbon::parse($request->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($request->fecha_fin));
        if ($days == 0) $days = 1;

        $totalPrice = $days * $car->precio;

        $rental = Rental::create([
            'id_vehiculo' => $car->id,
            'id_cliente' => Auth::user()->customer->id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'precio_total' => $totalPrice,
            'id_estado' => 1
        ]);

        $ownerUser = $car->vendedor->user;
        if ($ownerUser) {
            Mail::to($ownerUser->email)->send(new NewRentalRequest($rental));
        }

        return redirect()->route('sales.index')->with('success', 'Solicitud de alquiler enviada. Esperando aprobación del dueño.');
    }

    public function accept(Rental $rental)
    {
        if (Auth::user()->customer->id !== $rental->car->id_vendedor) {
            abort(403);
        }

        $rental->update(['id_estado' => 7]);

        $customerUser = $rental->customer->user;
        if ($customerUser) {
            Mail::to($customerUser->email)->send(new RentalAccepted($rental));
        }

        return redirect()->back()->with('success', 'Solicitud aceptada. Esperando pago del cliente.');
    }

    public function pay(Rental $rental)
    {
        if (Auth::user()->customer->id !== $rental->id_cliente) {
            abort(403);
        }

        if ($rental->id_estado !== 7) {
            return redirect()->back()->with('error', 'Este alquiler no está listo para pago.');
        }

        $initialStatus = \Carbon\Carbon::parse($rental->fecha_inicio)->isToday() ? 3 : 2;

        $rental->update(['id_estado' => $initialStatus]);
        $rental->car->update(['id_estado' => 6]);

        // Disparar evento en lugar de Job directo
        RentalPaid::dispatch($rental);

        return redirect()->route('sales.index')->with('success', 'Pago realizado. Alquiler confirmado.');
    }

    public function reject(Rental $rental)
    {
        if (Auth::user()->customer->id !== $rental->car->id_vendedor) {
            abort(403);
        }

        $rental->update(['id_estado' => 6]);

        $customerUser = $rental->customer->user;
        if ($customerUser) {
            Mail::to($customerUser->email)->send(new RentalRejected($rental));
        }

        return redirect()->back()->with('success', 'Solicitud de alquiler rechazada.');
    }
}
