<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Language Switcher Route (with Cookie)
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'es'])) {
        // Queue a cookie for 1 year (525600 minutes)
        Cookie::queue('locale', $locale, 525600);
    }
    return redirect()->back();
})->name('lang.switch');

Route::resource('cars', CarsController::class);

Route::middleware('auth')->group(function () {
    // My Cars Route
    Route::get('/my-cars', [CarsController::class, 'myCars'])->name('cars.my_cars');

    // Rutas de Ofertas
    Route::post('/cars/{car}/offer', [OfferController::class, 'store'])->name('offers.store');
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::post('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::post('/offers/{offer}/reject', [OfferController::class, 'reject'])->name('offers.reject');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
