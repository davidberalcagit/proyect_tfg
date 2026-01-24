<?php

use App\Mail\RentalReturnReminder;
use App\Models\Rental;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('rental return reminder email contains correct subject', function () {
    $rental = Rental::factory()->create();

    $mailable = new RentalReturnReminder($rental);

    $mailable->assertHasSubject('Recordatorio de Devolución de Vehículo');
});

test('rental return reminder email renders correctly', function () {
    $rental = Rental::factory()->create();

    $mailable = new RentalReturnReminder($rental);

    // Assert that the email contains the car title, which should be present in the view
    $mailable->assertSeeInHtml($rental->car->title);
});
