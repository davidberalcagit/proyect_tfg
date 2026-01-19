<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/cars');
    })->name('dashboard');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'es'])) {
        Cookie::queue('locale', $locale, 525600);
    }
    return redirect()->back();
})->name('lang.switch');

Route::resource('cars', CarsController::class);

Route::middleware('auth')->group(function () {
    Route::get('/my-cars', [CarsController::class, 'myCars'])->name('cars.my_cars');

    Route::post('/cars/{car}/status/sale', [CarsController::class, 'setStatusSale'])->name('cars.status.sale');
    Route::post('/cars/{car}/status/rent', [CarsController::class, 'setStatusRent'])->name('cars.status.rent');

    // Ofertas
    Route::post('/cars/{car}/offer', [OfferController::class, 'store'])->name('offers.store');
    Route::get('/transactions', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::post('/offers/{offer}/reject', [OfferController::class, 'reject'])->name('offers.reject');

    // Alquileres
    Route::get('/cars/{car}/rent', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/cars/{car}/rent', [RentalController::class, 'store'])->name('rentals.store');
    Route::post('/rentals/{rental}/accept', [RentalController::class, 'accept'])->name('rentals.accept'); // Nueva
    Route::post('/rentals/{rental}/reject', [RentalController::class, 'reject'])->name('rentals.reject'); // Nueva

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/run-job', [AdminController::class, 'runJob'])->name('admin.run-job');
    });

    // Supervisor
    Route::middleware(['role:supervisor|admin'])->group(function () {
        Route::get('/supervisor', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
        Route::post('/supervisor/approve/{id}', [SupervisorController::class, 'approveCar'])->name('supervisor.approve');
        Route::post('/supervisor/reject/{id}', [SupervisorController::class, 'rejectCar'])->name('supervisor.reject');
    });
});

require __DIR__.'/auth.php';
