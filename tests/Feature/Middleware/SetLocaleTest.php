<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

test('middleware sets locale from cookie', function () {
    Route::get('/test-locale', function () {
        return App::getLocale();
    })->middleware(SetLocale::class);

    $response = $this->withCookie('locale', 'es')
                     ->get('/test-locale');

    $response->assertSee('es');
});

test('middleware sets locale from session', function () {
    Route::get('/test-locale-session', function () {
        return App::getLocale();
    })->middleware(SetLocale::class);

    Session::put('locale', 'fr');

    $response = $this->get('/test-locale-session');

    $response->assertSee('fr');
});

test('middleware does nothing if no locale set', function () {
    Route::get('/test-locale-default', function () {
        return App::getLocale();
    })->middleware(SetLocale::class);

    $default = config('app.locale');

    $response = $this->get('/test-locale-default');

    $response->assertSee($default);
});
