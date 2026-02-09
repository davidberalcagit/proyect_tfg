<?php

use App\Models\User;

test('authenticated user is redirected to home after login', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);


    $response->assertRedirect('/dashboard');
});
