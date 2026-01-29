<?php

namespace Tests\Feature\Mail;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('welcome email content', function () {
    $user = User::factory()->create(['name' => 'Test User']);

    $mail = new WelcomeEmail($user);

    $mail->assertSeeInHtml('Test User');
    $mail->assertSeeInHtml('Bienvenido');
});
