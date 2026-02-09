<?php

use App\Mail\CarApproved;
use App\Models\Cars;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

test('car approved email contains correct data', function () {
    $car = Cars::factory()->create();

    $mailable = new CarApproved($car);

    $mailable->assertSeeInHtml($car->title);
    $mailable->assertSeeInHtml('aprobado');
});

test('car approved email has attachment', function () {
    Pdf::shouldReceive('loadView')
        ->andReturnSelf();

    Pdf::shouldReceive('output')
        ->andReturn('fake pdf content');

    $car = Cars::factory()->create();
    $mailable = new CarApproved($car);

    $attachments = $mailable->attachments();

    $found = false;
    foreach ($attachments as $attachment) {
       if (isset($attachment->as) && $attachment->as === 'Certificate.pdf') {
            $found = true;
            break;
        }
    }
    $this->assertCount(1, $attachments);
});
