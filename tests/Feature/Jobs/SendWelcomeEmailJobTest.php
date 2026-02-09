<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendWelcomeEmailJob;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('send welcome email job sends email', function () {
    Mail::fake();

    $user = User::factory()->create();
    $job = new SendWelcomeEmailJob($user);
    $job->handle();

    if (in_array(\Illuminate\Contracts\Queue\ShouldQueue::class, class_implements(WelcomeEmail::class))) {
        Mail::assertQueued(WelcomeEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    } else {
        Mail::assertSent(WelcomeEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
});
