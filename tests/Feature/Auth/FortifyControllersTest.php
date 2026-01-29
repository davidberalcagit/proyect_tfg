<?php

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Features;

uses(RefreshDatabase::class);

test('email verification prompt is displayed', function () {
    if (! Features::enabled(Features::emailVerification())) {
        $this->markTestSkipped('Email verification not enabled.');
    }

    $user = User::factory()->create(['email_verified_at' => null]);

    $response = $this->actingAs($user)->get('/email/verify');

    $response->assertStatus(200);
});

test('email verification notification can be resent', function () {
    if (! Features::enabled(Features::emailVerification())) {
        $this->markTestSkipped('Email verification not enabled.');
    }

    $user = User::factory()->create(['email_verified_at' => null]);

    $response = $this->actingAs($user)->post('/email/verification-notification');

    $response->assertRedirect();
    $response->assertSessionHas('status', 'verification-link-sent');
});

test('confirmable password controller confirms password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/user/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('reset user password action', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $action = new ResetUserPassword();

    $action->reset($user, [
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
