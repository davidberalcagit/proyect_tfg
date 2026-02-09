<?php

use App\Mail\NewOfferReceived;
use App\Mail\OfferAccepted;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('email is sent to seller when offer is made', function () {
    Mail::fake();

    $sellerUser = User::factory()->create();
    $sellerUser->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $sellerCustomer->id,
        'id_estado' => 1
    ]);

    $response = $this->actingAs($buyerUser)->post(route('offers.store', $car), [
        'cantidad' => 15000
    ]);

    $response->assertRedirect();

    Mail::assertSent(NewOfferReceived::class, function ($mail) use ($sellerUser) {
        return $mail->hasTo($sellerUser->email);
    });
});

test('email with pdf is sent to buyer and seller when offer is accepted', function () {
    Mail::fake();

    $sellerUser = User::factory()->create();
    $sellerUser->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $sellerCustomer->id,
        'id_estado' => 1
    ]);

    $offer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'cantidad' => 14000,
        'estado' => 'pending'
    ]);

    $this->actingAs($sellerUser)->post(route('offers.accept', $offer));

    Mail::assertSent(OfferAccepted::class, function ($mail) use ($buyerUser) {
        return $mail->hasTo($buyerUser->email);
    });
});
