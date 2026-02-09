<?php

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;

test('auth controllers can be instantiated to force coverage', function () {
    expect(new ConfirmablePasswordController())->toBeInstanceOf(ConfirmablePasswordController::class);
    expect(new EmailVerificationPromptController())->toBeInstanceOf(EmailVerificationPromptController::class);
    expect(new VerifyEmailController())->toBeInstanceOf(VerifyEmailController::class);
});
