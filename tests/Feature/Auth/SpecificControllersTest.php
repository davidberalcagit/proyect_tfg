<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('email verification prompt controller', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $controller = app(EmailVerificationPromptController::class);

    $request = Request::create('/email/verify', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = $controller->__invoke($request);

    expect($response->getStatusCode())->toBe(200);
});

test('email verification notification controller', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $controller = app(EmailVerificationNotificationController::class);

    $request = Request::create('/email/verification-notification', 'POST');
    $request->setUserResolver(fn () => $user);

    $response = $controller->store($request);

    expect($response->isRedirect())->toBeTrue();
});

test('confirmable password controller show', function () {
    $user = User::factory()->create();
    $controller = app(ConfirmablePasswordController::class);

    $request = Request::create('/user/confirm-password', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = $controller->show($request);

    expect($response->getStatusCode())->toBe(200);
});

test('verify email controller', function () {
    Event::fake();
    $user = User::factory()->create(['email_verified_at' => null]);
    $controller = app(VerifyEmailController::class);

    // Generate valid signature URL parts
    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
    );

    // Parse the URL to get parameters
    $components = parse_url($url);
    parse_str($components['query'], $query);

    $request = Request::create('/email/verify/'.$user->id.'/'.$query['hash'], 'GET', $query);
    $request->setUserResolver(fn () => $user);

    // Mock route parameters resolver since controller uses $request->route('id')
    $route = new \Illuminate\Routing\Route('GET', '/email/verify/{id}/{hash}', []);
    $route->bind($request);
    $route->setParameter('id', $user->id);
    $route->setParameter('hash', $query['hash']);
    $request->setRouteResolver(fn () => $route);

    $response = $controller->__invoke($request);

    expect($response->isRedirect())->toBeTrue();
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});
