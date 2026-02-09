<?php

namespace App\Http\Controllers;

use App\Events\OfferCreated;
use App\Events\SaleCompleted;
use App\Jobs\SendOfferAcceptedJob;
use App\Jobs\SendOfferRejectedJob;
use App\Models\Cars;
use App\Models\Offer;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OfferController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Cars $car)
    {
        $this->authorize('create', [Offer::class, $car]);

        $request->validate([
            'cantidad' => 'required|numeric|min:1'
        ]);

        $buyerId = Auth::user()->customer->id;

        $existingOffer = Offer::where('id_vehiculo', $car->id)
            ->where('id_comprador', $buyerId)
            ->whereIn('estado', ['pending', 'accepted_by_seller'])
            ->first();

        if ($existingOffer) {
            return redirect()->back()->with('error', 'Ya tienes una oferta activa por este coche.');
        }

        $offer = Offer::create([
            'id_vehiculo' => $car->id,
            'id_comprador' => $buyerId,
            'id_vendedor' => $car->id_vendedor,
            'cantidad' => $request->cantidad,
            'estado' => 'pending'
        ]);

        OfferCreated::dispatch($offer);

        return redirect()->back()->with('success', 'Oferta enviada al vendedor.');
    }

    public function index()
    {
        return redirect()->route('sales.index');
    }

    public function accept(Offer $offer)
    {
        $this->authorize('accept', $offer);

        $offer->update(['estado' => 'accepted_by_seller']);

        SendOfferAcceptedJob::dispatch($offer);

        return redirect()->route('sales.index')->with('success', 'Oferta aceptada. Esperando pago del comprador.');
    }

    public function pay(Offer $offer)
    {
        if (Auth::user()->customer->id !== $offer->id_comprador) {
            abort(403);
        }

        if ($offer->estado !== 'accepted_by_seller') {
            return redirect()->back()->with('error', 'Esta oferta no está lista para pago.');
        }

        $sale = Sales::create([
            'id_vehiculo' => $offer->id_vehiculo,
            'id_vendedor' => $offer->id_vendedor,
            'id_comprador' => $offer->id_comprador,
            'precio' => $offer->cantidad,
            'id_estado' => 1
        ]);

        $offer->update(['estado' => 'completed']);
        $offer->car->update(['id_estado' => 2]);

        Offer::where('id_vehiculo', $offer->id_vehiculo)
            ->where('id', '!=', $offer->id)
            ->where('estado', 'pending')
            ->update(['estado' => 'rejected']);

        SaleCompleted::dispatch($sale);

        return redirect()->route('sales.index')->with('success', 'Pago realizado y venta completada.');
    }

    public function reject(Offer $offer)
    {
        $this->authorize('reject', $offer);

        $offer->update(['estado' => 'rejected']);

        SendOfferRejectedJob::dispatch($offer);

        return redirect()->route('sales.index')->with('success', 'Oferta rechazada.');
    }
}
