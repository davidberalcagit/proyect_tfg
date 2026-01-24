<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Customers;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display the specified resource (Public Seller Profile).
     */
    public function show(Customers $customer)
    {
        // Cargar coches disponibles del vendedor
        $cars = Cars::where('id_vendedor', $customer->id)
                    ->available() // Scope available (estado 1 o 3)
                    ->latest()
                    ->paginate(12);

        return view('seller.show', compact('customer', 'cars'));
    }
}
