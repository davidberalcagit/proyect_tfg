<?php

namespace App\Http\Controllers;

use App\Jobs\SendOfferNotificationJob;
use App\Mail\OfferAccepted;
use App\Models\Cars;
use App\Models\Offer;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OfferController extends Controller
{
    // El comprador hace una oferta
    public function store(Request $request, Cars $car)
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión y tener perfil de cliente.');
        }

        // Verificar que el coche esté en venta (id_estado = 1)
        if ($car->id_estado !== 1) {
            return redirect()->back()->with('error', 'Este coche no está disponible para ofertas.');
        }

        $request->validate([
            'cantidad' => 'required|numeric|min:1'
        ]);

        $buyerId = Auth::user()->customer->id;

        if ($car->id_vendedor == $buyerId) {
            return redirect()->back()->with('error', 'No puedes ofertar por tu propio coche.');
        }

        // Verificar si ya hizo una oferta pendiente
        $existingOffer = Offer::where('id_vehiculo', $car->id)
            ->where('id_comprador', $buyerId)
            ->where('estado', 'pending')
            ->first();

        if ($existingOffer) {
            return redirect()->back()->with('error', 'Ya tienes una oferta pendiente por este coche.');
        }

        $offer = Offer::create([
            'id_vehiculo' => $car->id,
            'id_comprador' => $buyerId,
            'id_vendedor' => $car->id_vendedor,
            'cantidad' => $request->cantidad,
            'estado' => 'pending'
        ]);

        // Enviar correo al vendedor usando Job
        SendOfferNotificationJob::dispatch($offer);

        return redirect()->back()->with('success', 'Oferta enviada al vendedor.');
    }

    // El vendedor ve las ofertas recibidas
    public function index()
    {
        $sellerId = Auth::user()->customer->id;

        $offers = Offer::where('id_vendedor', $sellerId)
            ->where('estado', 'pending')
            ->with(['car', 'buyer'])
            ->get();

        return view('offers.index', compact('offers'));
    }

    // Aceptar oferta -> Crea Venta
    public function accept(Offer $offer)
    {
        if (Auth::user()->customer->id !== $offer->id_vendedor) {
            abort(403);
        }

        // Crear registro en Sales (id_estado = 1 -> Completada)
        Sales::create([
            'id_vehiculo' => $offer->id_vehiculo,
            'id_vendedor' => $offer->id_vendedor,
            'id_comprador' => $offer->id_comprador,
            'precio' => $offer->cantidad,
            'id_estado' => 1
        ]);

        // Marcar oferta como aceptada
        $offer->update(['estado' => 'accepted']);

        // Actualizar estado del coche a Vendido (id_estado = 2)
        $offer->car->update(['id_estado' => 2]);

        // Rechazar el resto de ofertas pendientes para este coche
        Offer::where('id_vehiculo', $offer->id_vehiculo)
            ->where('id', '!=', $offer->id)
            ->where('estado', 'pending')
            ->update(['estado' => 'rejected']);

        // Enviar correo al comprador
        $buyerUser = $offer->buyer->user;
        if ($buyerUser) {
            Mail::to($buyerUser->email)->send(new OfferAccepted($offer));
        }

        // Enviar correo al vendedor (también recibe el recibo)
        $sellerUser = $offer->seller->user;
        if ($sellerUser) {
            Mail::to($sellerUser->email)->send(new OfferAccepted($offer));
        }

        return redirect()->route('offers.index')->with('success', 'Oferta aceptada y venta procesada.');
    }

    public function reject(Offer $offer)
    {
        if (Auth::user()->customer->id !== $offer->id_vendedor) {
            abort(403);
        }

        $offer->update(['estado' => 'rejected']);

        return redirect()->route('offers.index')->with('success', 'Oferta rechazada.');
    }
}
