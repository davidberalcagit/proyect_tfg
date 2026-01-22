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

    public function toggle(Cars $car)
    {
        $user = Auth::user();

        if ($user->favorites()->where('car_id', $car->id)->exists()) {
            $user->favorites()->detach($car->id);
            $message = 'Eliminado de favoritos.';
        } else {
            $user->favorites()->attach($car->id);
            $message = 'Añadido a favoritos.';
        }

        return redirect()->back()->with('success', $message);
    }
}
