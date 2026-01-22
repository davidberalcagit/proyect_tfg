<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Rental;
use App\Models\Sales;
use Barryvdh\DomPDF\Facade\Pdf;
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

        // Usar Scope: pending() y forSeller()
        // Nota: forSeller ya incluye with() y latest()
        $receivedOffers = Offer::forSeller($customerId)
            ->whereIn('estado', ['pending', 'accepted_by_seller'])
            ->get();

        $sentOffers = Offer::where('id_comprador', $customerId)
            ->whereIn('estado', ['pending', 'accepted_by_seller', 'rejected'])
            ->with(['car', 'seller'])
            ->get();

        $purchases = Sales::where('id_comprador', $customerId)
            ->with(['vehiculo', 'vendedor', 'status'])
            ->latest()
            ->get();

        $sales = Sales::where('id_vendedor', $customerId)
            ->with(['vehiculo', 'comprador', 'status'])
            ->latest()
            ->get();

        $rentals = Rental::where('id_cliente', $customerId)
            ->with(['car', 'status'])
            ->latest()
            ->get();

        $myRentalsAsOwner = Rental::whereHas('car', function ($query) use ($customerId) {
                $query->where('id_vendedor', $customerId);
            })
            ->with(['car', 'customer', 'status'])
            ->latest()
            ->get();

        return view('sales.index', compact('receivedOffers', 'sentOffers', 'purchases', 'sales', 'rentals', 'myRentalsAsOwner'));
    }

    public function downloadReceipt(Sales $sale)
    {
        $userCustomer = Auth::user()->customer;
        if (!$userCustomer || ($sale->id_comprador !== $userCustomer->id && $sale->id_vendedor !== $userCustomer->id)) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.sale_receipt', ['sale' => $sale]);
        return $pdf->download('Recibo_Venta_' . $sale->id . '.pdf');
    }

    public function downloadRentalReceipt(Rental $rental)
    {
        $userCustomer = Auth::user()->customer;
        if (!$userCustomer || ($rental->id_cliente !== $userCustomer->id && $rental->car->id_vendedor !== $userCustomer->id)) {
            abort(403);
        }

        if (in_array($rental->id_estado, [0, 1, 7, 6])) {
             abort(403, 'Recibo no disponible.');
        }

        $pdf = Pdf::loadView('pdf.rental_receipt', ['rental' => $rental]);
        return $pdf->download('Recibo_Alquiler_' . $rental->id . '.pdf');
    }

    public function downloadSaleTerms()
    {
        $pdf = Pdf::loadView('pdf.sale_terms');
        return $pdf->stream('Terminos_Compraventa.pdf');
    }
}
