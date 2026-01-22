<?php

use App\Jobs\SendCarApprovedNotificationJob;
use App\Jobs\SendCarRejectedNotificationJob;
use App\Jobs\SendOfferAcceptedJob;
use App\Jobs\SendOfferNotificationJob;
use App\Jobs\SendOfferRejectedJob;
use App\Jobs\SendRentalProcessedJob;
use App\Jobs\SendSaleProcessedJob;
use App\Mail\CarApproved;
use App\Mail\CarRejected;
use App\Mail\NewOfferReceived;
use App\Mail\NewRentalRequest;
use App\Mail\OfferAccepted;
use App\Mail\OfferRejected;
use App\Mail\RentalAccepted;
use App\Mail\RentalProcessed;
use App\Mail\RentalRejected;
use App\Mail\SaleProcessed;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

// Helper function
function createCustomer() {
    $user = User::factory()->create();
    $user->assignRole('individual');
    return Customers::factory()->create(['id_usuario' => $user->id]);
}

test('new offer email is sent to seller', function () {
    Mail::fake();
    $seller = createCustomer();
    $buyer = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);
    $offer = Offer::create(['id_vehiculo' => $car->id, 'id_vendedor' => $seller->id, 'id_comprador' => $buyer->id, 'cantidad' => 1000, 'estado' => 'pending']);

    (new SendOfferNotificationJob($offer))->handle();

    // Este se envía síncronamente
    Mail::assertSent(NewOfferReceived::class, fn ($mail) => $mail->hasTo($seller->user->email));
});

test('offer accepted email is sent to buyer', function () {
    Mail::fake();
    $seller = createCustomer();
    $buyer = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);
    $offer = Offer::create(['id_vehiculo' => $car->id, 'id_vendedor' => $seller->id, 'id_comprador' => $buyer->id, 'cantidad' => 1000, 'estado' => 'accepted_by_seller']);

    (new SendOfferAcceptedJob($offer))->handle();

    // Este se envía síncronamente
    Mail::assertSent(OfferAccepted::class, fn ($mail) => $mail->hasTo($buyer->user->email));
});

test('offer rejected email is sent to buyer', function () {
    Mail::fake();
    $seller = createCustomer();
    $buyer = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);
    $offer = Offer::create(['id_vehiculo' => $car->id, 'id_vendedor' => $seller->id, 'id_comprador' => $buyer->id, 'cantidad' => 1000, 'estado' => 'rejected']);

    (new SendOfferRejectedJob($offer))->handle();

    Mail::assertQueued(OfferRejected::class, fn ($mail) => $mail->hasTo($buyer->user->email));
});

test('sale processed email is sent to both', function () {
    Mail::fake();
    $seller = createCustomer();
    $buyer = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);
    $sale = Sales::create(['id_vehiculo' => $car->id, 'id_vendedor' => $seller->id, 'id_comprador' => $buyer->id, 'precio' => 1000, 'id_estado' => 1]);

    (new SendSaleProcessedJob($sale))->handle();

    Mail::assertQueued(SaleProcessed::class, fn ($mail) => $mail->hasTo($buyer->user->email));
    Mail::assertQueued(SaleProcessed::class, fn ($mail) => $mail->hasTo($seller->user->email));
});

test('rental request email is sent to owner', function () {
    Mail::fake();
    $owner = createCustomer();
    $renter = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $owner->id, 'id_estado' => 3]);
    $rental = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $renter->id, 'fecha_inicio' => now(), 'fecha_fin' => now()->addDays(1), 'precio_total' => 100, 'id_estado' => 1]);

    Mail::to($owner->user->email)->send(new NewRentalRequest($rental));

    Mail::assertQueued(NewRentalRequest::class, fn ($mail) => $mail->hasTo($owner->user->email));
});

test('rental accepted email is sent to renter', function () {
    Mail::fake();
    $owner = createCustomer();
    $renter = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $owner->id]);
    $rental = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $renter->id, 'fecha_inicio' => now(), 'fecha_fin' => now()->addDays(1), 'precio_total' => 100, 'id_estado' => 7]);

    Mail::to($renter->user->email)->send(new RentalAccepted($rental));

    Mail::assertQueued(RentalAccepted::class, fn ($mail) => $mail->hasTo($renter->user->email));
});

test('rental rejected email is sent to renter', function () {
    Mail::fake();
    $owner = createCustomer();
    $renter = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $owner->id]);
    $rental = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $renter->id, 'fecha_inicio' => now(), 'fecha_fin' => now()->addDays(1), 'precio_total' => 100, 'id_estado' => 6]);

    Mail::to($renter->user->email)->send(new RentalRejected($rental));

    Mail::assertQueued(RentalRejected::class, fn ($mail) => $mail->hasTo($renter->user->email));
});

test('rental processed email is sent to both', function () {
    Mail::fake();
    $owner = createCustomer();
    $renter = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $owner->id]);
    $rental = Rental::create(['id_vehiculo' => $car->id, 'id_cliente' => $renter->id, 'fecha_inicio' => now(), 'fecha_fin' => now()->addDays(1), 'precio_total' => 100, 'id_estado' => 2]);

    (new SendRentalProcessedJob($rental))->handle();

    Mail::assertQueued(RentalProcessed::class, fn ($mail) => $mail->hasTo($renter->user->email));
    Mail::assertQueued(RentalProcessed::class, fn ($mail) => $mail->hasTo($owner->user->email));
});

test('car approved email is sent to seller', function () {
    Mail::fake();
    $seller = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);

    (new SendCarApprovedNotificationJob($car))->handle();

    Mail::assertQueued(CarApproved::class, fn ($mail) => $mail->hasTo($seller->user->email));
});

test('car rejected email is sent to seller', function () {
    Mail::fake();
    $seller = createCustomer();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);

    (new SendCarRejectedNotificationJob($car, 'Razón test'))->handle();

    Mail::assertQueued(CarRejected::class, fn ($mail) => $mail->hasTo($seller->user->email));
});
