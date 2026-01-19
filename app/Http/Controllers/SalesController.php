<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return redirect()->route('login');
        }

        $customerId = Auth::user()->customer->id;

        // 1. Ofertas Recibidas (Pendientes) - Ya se pasa desde OfferController? No, aquí lo consultamos de nuevo o lo pasamos.
        // Espera, en el código anterior lo pasé. Lo mantengo.
        $receivedOffers = \App\Models\Offer::where('id_vendedor', $customerId)
            ->where('estado', 'pending')
            ->with(['car', 'buyer'])
            ->get();

        // 2. Mis Compras (Yo compré)
        $purchases = Sales::where('id_comprador', $customerId)
            ->with(['vehiculo', 'vendedor', 'status'])
            ->latest()
            ->get();

        // 3. Mis Ventas (Yo vendí)
        $sales = Sales::where('id_vendedor', $customerId)
            ->with(['vehiculo', 'comprador', 'status'])
            ->latest()
            ->get();

        // 4. Mis Alquileres (Yo alquilé un coche de otro)
        $rentals = Rental::where('id_cliente', $customerId)
            ->with(['car', 'status'])
            ->latest()
            ->get();

        // 5. Mis Arrendamientos (Yo alquilé MI coche a otro)
        // Buscamos rentals donde el coche pertenece al usuario actual
        $myRentalsAsOwner = Rental::whereHas('car', function ($query) use ($customerId) {
                $query->where('id_vendedor', $customerId);
            })
            ->with(['car', 'customer', 'status'])
            ->latest()
            ->get();

        return view('sales.index', compact('receivedOffers', 'purchases', 'sales', 'rentals', 'myRentalsAsOwner'));
    }
}
