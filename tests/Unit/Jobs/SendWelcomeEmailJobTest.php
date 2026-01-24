<?php

use App\Jobs\SendWelcomeEmailJob;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('send welcome email job queues email', function () {
    Mail::fake();

    $user = User::factory()->create();

    $job = new SendWelcomeEmailJob($user);
    $job->handle();

    // Since WelcomeEmail implements ShouldQueue, it is queued, not sent immediately.
    Mail::assertQueued(WelcomeEmail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});
