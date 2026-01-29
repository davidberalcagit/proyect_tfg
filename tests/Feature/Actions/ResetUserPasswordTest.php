<?php

namespace Tests\Feature\Actions;

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('password can be reset', function () {
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

test('password validation fails', function () {
    $user = User::factory()->create();
    $action = new ResetUserPassword();

    $action->reset($user, [
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);
})->throws(ValidationException::class);
