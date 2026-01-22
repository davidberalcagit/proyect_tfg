<?php

use App\Events\CarCreated;
use App\Events\CarRejected;
use App\Events\OfferCreated;
use App\Events\RentalPaid;
use App\Events\SaleCompleted;
use App\Jobs\SendCarRejectedNotificationJob;
use App\Jobs\SendOfferNotificationJob;
use App\Jobs\SendRentalProcessedJob;
use App\Jobs\SendSaleProcessedJob;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('car created event is dispatched', function () {
    Event::fake([CarCreated::class]);
    Storage::fake('public');

    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $file = UploadedFile::fake()->image('car.jpg');

    $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'TestBrand',
        'temp_model' => 'TestModel',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'EVENT123',
        'anyo_matri' => 2024,
        'km' => 100,
        'precio' => 20000,
        'descripcion' => 'Test',
        'image' => $file,
        'id_listing_type' => 1
    ]);

    Event::assertDispatched(CarCreated::class);
});

test('offer created event dispatches job', function () {
    Bus::fake();

    $seller = Customers::factory()->create();
    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    $car = Cars::factory()->create(['id_vendedor' => $seller->id, 'id_estado' => 1]);

    $this->actingAs($buyerUser)->post(route('offers.store', $car), [
        'cantidad' => 15000
    ]);

    Bus::assertDispatched(SendOfferNotificationJob::class);
});

test('sale completed event dispatches job', function () {
    Bus::fake();

    $seller = Customers::factory()->create();
    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    $buyer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    $car = Cars::factory()->create(['id_vendedor' => $seller->id]);

    $offer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $seller->id,
        'id_comprador' => $buyer->id,
        'cantidad' => 1000,
        'estado' => 'accepted_by_seller'
    ]);

    $this->actingAs($buyerUser)->post(route('offers.pay', $offer));

    Bus::assertDispatched(SendSaleProcessedJob::class);
});

test('rental paid event dispatches job', function () {
    Bus::fake();

    $owner = Customers::factory()->create();
    $renterUser = User::factory()->create();
    $renterUser->assignRole('individual');
    $renter = Customers::factory()->create(['id_usuario' => $renterUser->id]);

    $car = Cars::factory()->create(['id_vendedor' => $owner->id, 'id_estado' => 3]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $renter->id,
        'fecha_inicio' => now()->addDay(),
        'fecha_fin' => now()->addDays(2),
        'precio_total' => 100,
        'id_estado' => 7 // Aceptado
    ]);

    $this->actingAs($renterUser)->post(route('rentals.pay', $rental));

    Bus::assertDispatched(SendRentalProcessedJob::class);
});

test('car rejected event dispatches job', function () {
    Bus::fake();

    $supervisorUser = User::factory()->create();
    $supervisorUser->assignRole('supervisor');

    $car = Cars::factory()->create(['id_estado' => 4]); // Pendiente

    $this->actingAs($supervisorUser)->post(route('supervisor.reject', $car), [
        'reason' => 'Test Reason'
    ]);

    Bus::assertDispatched(SendCarRejectedNotificationJob::class);
});
