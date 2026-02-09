<?php

use App\Jobs\ProcessCarImageJob;
use App\Jobs\SendOfferAcceptedJob;
use App\Jobs\SendOfferNotificationJob;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('process car image job is dispatched when car is created', function () {
    Bus::fake();
    Storage::fake('public');

    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $file = UploadedFile::fake()->image('car.jpg');

    $response = $this->actingAs($user)->post('/cars', [
        'temp_brand' => 'MarcaJob',
        'temp_model' => 'ModeloJob',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'JOB123',
        'anyo_matri' => 2024,
        'km' => 100,
        'precio' => 25000,
        'descripcion' => 'Test description',
        'image' => $file,
        'id_listing_type' => 1
    ]);

    $this->assertDatabaseHas('cars', ['matricula' => 'JOB123']);

    Bus::assertDispatched(ProcessCarImageJob::class);
});

test('send offer notification job is dispatched when offer is created', function () {
    Bus::fake();

    $sellerUser = User::factory()->create();
    $sellerUser->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $sellerCustomer->id,
        'id_estado' => 1
    ]);

    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    $this->actingAs($buyerUser)->post(route('offers.store', $car), [
        'cantidad' => 15000
    ]);

    Bus::assertDispatched(SendOfferNotificationJob::class);
});

test('send offer accepted job is dispatched when offer is accepted', function () {
    Bus::fake();

    $sellerUser = User::factory()->create();
    $sellerUser->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $sellerCustomer->id,
        'id_estado' => 1
    ]);

    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    $offer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'cantidad' => 15000,
        'estado' => 'pending'
    ]);

    $this->actingAs($sellerUser)->post(route('offers.accept', $offer));

    Bus::assertDispatched(SendOfferAcceptedJob::class);
});
