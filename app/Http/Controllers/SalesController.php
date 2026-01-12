<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function buy(Request $request, Cars $car)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to buy a car.');
        }

        $user = Auth::user();

        // Ensure user has a customer profile
        if (!$user->customer) {
             return redirect()->back()->with('error', 'You need a customer profile to buy a car.');
        }

        // Prevent buying own car
        if ($car->id_vendedor == $user->customer->id) {
            return redirect()->back()->with('error', 'You cannot buy your own car.');
        }

        // Create Sale record
        Sales::create([
            'id_vehiculo' => $car->id,
            'id_vendedor' => $car->id_vendedor,
            'id_comprador' => $user->customer->id,
            'precio' => $car->precio,
            'fecha_venta' => now(),
            'estado' => 'completed' // Assuming simple flow
        ]);

        // Optionally mark car as sold or delete it?
        // For now, let's just record the sale.
        // If we want to hide sold cars, we would need a status column on cars table.

        return redirect()->route('cars.index')->with('success', 'Car purchased successfully!');
    }
}
