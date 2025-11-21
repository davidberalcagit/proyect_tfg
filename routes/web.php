<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/blog', [PostController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('blog');
Route::get('/about', function () {
    return view('about');
})->middleware(['auth', 'verified'])->name('about');
Route::get('/contact', function () {
    return view('contact');
})->middleware(['auth', 'verified'])->name('contact');
Route::resource('posts', PostController::class);



//Route::get('/posts.index', function () {
//    return view('posts.index');
//})->middleware(['auth', 'verified'])->name('index.index');
//
//Route::get('/posts.create', function () {
//    return view('posts.create');
//})->middleware(['auth', 'verified'])->name('posts.create');
//Route::get('/posts.show', function () {
//    return view('posts.show');
//})->middleware(['auth', 'verified'])->name('show.index');
//Route::get('/posts.edit', function () {
//    return view('posts.edit');
//})->middleware(['auth', 'verified'])->name('edit.index');
Route::resource('posts', PostController::class)->middleware(['auth', 'verified']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
