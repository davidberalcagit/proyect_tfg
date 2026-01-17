<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
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
    // Redirigir /dashboard a /cars explícitamente
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

// Language Switcher Route (with Cookie)
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'es'])) {
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

    // --- RUTAS DE ADMIN ---
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/run-job', [AdminController::class, 'runJob'])->name('admin.run-job');
    });

    // --- RUTAS DE SUPERVISOR ---
    Route::middleware(['role:supervisor|admin'])->group(function () {
        Route::get('/supervisor', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
        Route::post('/supervisor/approve/{id}', [SupervisorController::class, 'approveCar'])->name('supervisor.approve');
        Route::post('/supervisor/reject/{id}', [SupervisorController::class, 'rejectCar'])->name('supervisor.reject');
    });
});

require __DIR__.'/auth.php';
