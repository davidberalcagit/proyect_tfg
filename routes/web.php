<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CustomerController;
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
        return redirect('/');
    })->name('dashboard');
});



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

Route::get('/seller/{customer}', [CustomerController::class, 'show'])->name('seller.show');

Route::middleware('auth')->group(function () {
    Route::get('/my-cars', [CarsController::class, 'myCars'])->name('cars.my_cars');

    Route::get('/my-favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/cars/{car}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::post('/cars/{car}/status/sale', [CarsController::class, 'setStatusSale'])->name('cars.status.sale');
    Route::post('/cars/{car}/status/rent', [CarsController::class, 'setStatusRent'])->name('cars.status.rent');

    Route::get('/sales/terms', [SalesController::class, 'downloadSaleTerms'])->name('sales.terms');
    Route::post('/cars/{car}/offer', [OfferController::class, 'store'])->name('offers.store');
    Route::get('/transactions', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/export', [SalesController::class, 'export'])->name('sales.export');
    Route::get('/sales/{sale}/receipt', [SalesController::class, 'downloadReceipt'])->name('sales.receipt');
    Route::get('/rentals/{rental}/receipt', [SalesController::class, 'downloadRentalReceipt'])->name('rentals.receipt');

    Route::post('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::post('/offers/{offer}/reject', [OfferController::class, 'reject'])->name('offers.reject');
    Route::post('/offers/{offer}/pay', [OfferController::class, 'pay'])->name('offers.pay');

    Route::get('/rentals/terms', [RentalController::class, 'downloadTerms'])->name('rentals.terms');
    Route::get('/cars/{car}/rent', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/cars/{car}/rent', [RentalController::class, 'store'])->name('rentals.store');
    Route::post('/rentals/{rental}/accept', [RentalController::class, 'accept'])->name('rentals.accept');
    Route::post('/rentals/{rental}/reject', [RentalController::class, 'reject'])->name('rentals.reject');
    Route::post('/rentals/{rental}/pay', [RentalController::class, 'pay'])->name('rentals.pay');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/run-job', [AdminController::class, 'runJob'])->name('admin.run-job');
    });

    Route::middleware(['role:supervisor|admin'])->group(function () {
        Route::get('/supervisor', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
        Route::get('/supervisor/report', [SupervisorController::class, 'downloadReport'])->name('supervisor.report');
        Route::post('/supervisor/approve/{id}', [SupervisorController::class, 'approveCar'])->name('supervisor.approve');
        Route::post('/supervisor/reject/{id}', [SupervisorController::class, 'rejectCar'])->name('supervisor.reject');
    });

    Route::middleware(['role:soporte|admin'])->group(function () {
        Route::get('/support/users', [SupportController::class, 'index'])->name('support.users.index');
        Route::get('/support/users/create', [SupportController::class, 'create'])->name('support.users.create');
        Route::post('/support/users', [SupportController::class, 'store'])->name('support.users.store');
        Route::get('/support/users/{user}', [SupportController::class, 'show'])->name('support.users.show');
        Route::get('/support/users/{user}/edit', [SupportController::class, 'edit'])->name('support.users.edit');
        Route::put('/support/users/{user}', [SupportController::class, 'update'])->name('support.users.update');
        Route::delete('/support/users/{user}', [SupportController::class, 'destroy'])->name('support.users.destroy');
    });
});

require __DIR__.'/auth.php';
