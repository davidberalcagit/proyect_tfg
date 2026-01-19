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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OfferController extends Controller
{
    use AuthorizesRequests;

    // El comprador hace una oferta
    public function store(Request $request, Cars $car)
    {
        $this->authorize('create', [Offer::class, $car]);

        $request->validate([
            'cantidad' => 'required|numeric|min:1'
        ]);

        $buyerId = Auth::user()->customer->id;

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

        SendOfferNotificationJob::dispatch($offer);

        return redirect()->back()->with('success', 'Oferta enviada al vendedor.');
    }

    // El vendedor ve las ofertas recibidas (YA NO SE USA DIRECTAMENTE, SE USA SALESCONTROLLER)
    // Pero lo mantengo por si acaso se llama desde API o algo, aunque debería redirigir a sales.index
    public function index()
    {
        return redirect()->route('sales.index');
    }

    // Aceptar oferta -> Crea Venta
    public function accept(Offer $offer)
    {
        $this->authorize('accept', $offer);

        Sales::create([
            'id_vehiculo' => $offer->id_vehiculo,
            'id_vendedor' => $offer->id_vendedor,
            'id_comprador' => $offer->id_comprador,
            'precio' => $offer->cantidad,
            'id_estado' => 1
        ]);

        $offer->update(['estado' => 'accepted']);
        $offer->car->update(['id_estado' => 2]);

        Offer::where('id_vehiculo', $offer->id_vehiculo)
            ->where('id', '!=', $offer->id)
            ->where('estado', 'pending')
            ->update(['estado' => 'rejected']);

        $buyerUser = $offer->buyer->user;
        if ($buyerUser) {
            Mail::to($buyerUser->email)->send(new OfferAccepted($offer));
        }

        $sellerUser = $offer->seller->user;
        if ($sellerUser) {
            Mail::to($sellerUser->email)->send(new OfferAccepted($offer));
        }

        return redirect()->route('sales.index')->with('success', 'Oferta aceptada y venta procesada.');
    }

    public function reject(Offer $offer)
    {
        $this->authorize('reject', $offer);

        $offer->update(['estado' => 'rejected']);

        return redirect()->route('sales.index')->with('success', 'Oferta rechazada.');
    }
}
