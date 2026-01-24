<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $cars = Auth::user()->favorites()->paginate(20);
        return view('cars.favorites', compact('cars'));
    }

    public function toggle(Request $request, Cars $car)
    {
        $user = Auth::user();
        $attached = false;

        if ($user->favorites()->where('car_id', $car->id)->exists()) {
            $user->favorites()->detach($car->id);
            $message = 'Eliminado de favoritos.';
            $attached = false;
        } else {
            $user->favorites()->attach($car->id);
            $message = 'Añadido a favoritos.';
            $attached = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'attached' => $attached,
                'message' => $message
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
