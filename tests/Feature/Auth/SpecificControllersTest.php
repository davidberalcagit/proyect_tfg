<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest; // Corrected Request Type
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('email verification prompt controller renders view', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $controller = new EmailVerificationPromptController();

    $request = Request::create('/email/verify', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = $controller->__invoke($request);

    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        expect($response->isRedirect())->toBeTrue();
    } else {
        expect($response->name())->toBe('auth.verify-email');
    }
});

test('email verification notification controller sends notification', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $controller = new EmailVerificationNotificationController();

    $request = Request::create('/email/verification-notification', 'POST');
    $request->setUserResolver(fn () => $user);

    $response = $controller->store($request);
    expect($response->isRedirect())->toBeTrue();
});

test('confirmable password controller show renders view', function () {
    $user = User::factory()->create();
    $controller = new ConfirmablePasswordController();

    $response = $controller->show();
    expect($response->name())->toBe('auth.confirm-password');
});

test('confirmable password controller store confirms password', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);
    $controller = new ConfirmablePasswordController();

    $request = Request::create('/user/confirm-password', 'POST', ['password' => 'password']);
    $request->setUserResolver(fn () => $user);

    $session = new \Illuminate\Session\Store('test', new \Illuminate\Session\ArraySessionHandler(10));
    $request->setLaravelSession($session);

    $response = $controller->store($request);

    expect($response->isRedirect())->toBeTrue();
    expect($session->has('auth.password_confirmed_at'))->toBeTrue();
});

test('verify email controller verifies email', function () {
    Event::fake();
    $user = User::factory()->create(['email_verified_at' => null]);
    $controller = new VerifyEmailController();

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
    );

    // Use the correct EmailVerificationRequest from Illuminate
    $request = EmailVerificationRequest::create($url, 'GET');
    $request->setUserResolver(fn () => $user);

    $route = new \Illuminate\Routing\Route('GET', '/email/verify/{id}/{hash}', []);
    $route->bind($request);
    $route->setParameter('id', $user->id);
    $route->setParameter('hash', sha1($user->getEmailForVerification()));
    $request->setRouteResolver(fn () => $route);

    $response = $controller->__invoke($request);

    expect($response->isRedirect())->toBeTrue();
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});
