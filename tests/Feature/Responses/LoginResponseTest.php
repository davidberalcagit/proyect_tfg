<?php

namespace Tests\Feature\Responses;

use App\Http\Responses\LoginResponse;
use Illuminate\Http\Request;

test('login response redirects to cars for web request', function () {
    $request = Request::create('/login', 'POST');
    $request->headers->set('Accept', 'text/html');

    $response = (new LoginResponse())->toResponse($request);

    expect($response->isRedirect())->toBeTrue();
    expect($response->getTargetUrl())->toBe(url('/cars'));
});

test('login response returns json for json request', function () {
    $request = Request::create('/login', 'POST');
    $request->headers->set('Accept', 'application/json');

    $response = (new LoginResponse())->toResponse($request);

    expect($response->getStatusCode())->toBe(200);
    expect($response->getContent())->toBe('{"two_factor":false}');
});
