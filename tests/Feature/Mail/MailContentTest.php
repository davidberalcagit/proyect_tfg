<?php

use App\Mail\CarRejected;
use App\Mail\NewOfferReceived;
use App\Mail\OfferAccepted;
use App\Mail\OfferRejected;
use App\Mail\SaleProcessed;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Sales;
use App\Models\SaleStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('sale processed mail content', function () {
    SaleStatus::firstOrCreate(['id' => 1], ['nombre' => 'Pendiente']);

    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyer->id]);

    $car = Cars::factory()->create();

    $sale = Sales::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'precio' => 10000,
        'id_estado' => 1
    ]);

    $mail = new SaleProcessed($sale);

    $mail->assertSeeInHtml($car->title);
    $mail->assertSeeInHtml('10,000');

    expect($mail->envelope()->subject)->toBe('Venta Procesada - Recibo Adjunto');
    expect($mail->attachments())->toBeArray();
});

test('new offer received mail content', function () {
    $car = Cars::factory()->create();
    $buyer = Customers::factory()->create();
    $seller = Customers::factory()->create();

    $offer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_comprador' => $buyer->id,
        'id_vendedor' => $seller->id,
        'cantidad' => 5000,
        'estado' => 'pending'
    ]);

    $mail = new NewOfferReceived($offer);

    $mail->assertSeeInHtml($car->title);
    $mail->assertSeeInHtml('5000');

    expect($mail->envelope()->subject)->toContain('New Offer Received');
    expect($mail->attachments())->toBeArray();
});

test('offer accepted mail content', function () {
    $car = Cars::factory()->create();
    $buyer = Customers::factory()->create();
    $seller = Customers::factory()->create();

    $offer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_comprador' => $buyer->id,
        'id_vendedor' => $seller->id,
        'cantidad' => 5000,
        'estado' => 'accepted_by_seller'
    ]);

    $mail = new OfferAccepted($offer);

    $mail->assertSeeInHtml($car->title);
    expect($mail->envelope()->subject)->toBe('¡Tu oferta ha sido aceptada!');
    expect($mail->attachments())->toBeArray();
});

test('offer rejected mail content', function () {
    $car = Cars::factory()->create();
    $buyer = Customers::factory()->create();
    $seller = Customers::factory()->create();

    $offer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_comprador' => $buyer->id,
        'id_vendedor' => $seller->id,
        'cantidad' => 5000,
        'estado' => 'rejected'
    ]);

    $mail = new OfferRejected($offer);

    $mail->assertSeeInHtml($car->title);
    expect($mail->envelope()->subject)->toBe('Tu oferta ha sido rechazada');
    expect($mail->attachments())->toBeArray();
});

test('car rejected mail content', function () {
    $car = Cars::factory()->create();
    $reason = 'Invalid photos';

    $mail = new CarRejected($car, $reason);

    $mail->assertSeeInHtml($car->title);
    $mail->assertSeeInHtml($reason);
    expect($mail->envelope()->subject)->toBe('Tu coche ha sido rechazado');
    expect($mail->attachments())->toBeArray();
});
