<?php

use App\Jobs\SendRentalProcessedJob;
use App\Mail\NewRentalRequest;
use App\Mail\RentalAccepted;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
    RentalStatus::firstOrCreate(['id' => 7], ['nombre' => 'Aceptado por dueño']);
});

test('full rental flow request accept pay', function () {
    Bus::fake();
    Mail::fake();

    $ownerUser = User::factory()->create();
    $ownerUser->assignRole('individual');
    $ownerCustomer = Customers::factory()->create(['id_usuario' => $ownerUser->id]);

    $renterUser = User::factory()->create();
    $renterUser->assignRole('individual');
    $renterCustomer = Customers::factory()->create(['id_usuario' => $renterUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $ownerCustomer->id,
        'id_estado' => 3,
        'precio' => 50
    ]);

    $response = $this->actingAs($renterUser)->post(route('rentals.store', $car), [
        'fecha_inicio' => now()->addDay()->format('Y-m-d'),
        'fecha_fin' => now()->addDays(3)->format('Y-m-d'),
    ]);

    $response->assertRedirect();

    $rental = Rental::where('id_vehiculo', $car->id)->first();
    expect($rental)->not->toBeNull();
    expect($rental->id_estado)->toBe(1);

    Mail::assertQueued(NewRentalRequest::class, fn ($mail) => $mail->hasTo($ownerUser->email));

    $response = $this->actingAs($ownerUser)->post(route('rentals.accept', $rental));
    $response->assertRedirect();

    $rental->refresh();
    expect($rental->id_estado)->toBe(7);

    Mail::assertQueued(RentalAccepted::class, fn ($mail) => $mail->hasTo($renterUser->email));

    $response = $this->actingAs($renterUser)->post(route('rentals.pay', $rental));
    $response->assertRedirect();

    $rental->refresh();
    expect($rental->id_estado)->toBe(2);

    $car->refresh();
    expect($car->id_estado)->toBe(6);

    Bus::assertDispatched(SendRentalProcessedJob::class);
});

test('owner can reject rental request', function () {
    $ownerUser = User::factory()->create();
    $ownerUser->assignRole('individual');
    $ownerCustomer = Customers::factory()->create(['id_usuario' => $ownerUser->id]);

    $renterUser = User::factory()->create();
    $renterUser->assignRole('individual');
    $renterCustomer = Customers::factory()->create(['id_usuario' => $renterUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $ownerCustomer->id,
        'id_estado' => 3
    ]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $renterCustomer->id,
        'fecha_inicio' => now()->addDay(),
        'fecha_fin' => now()->addDays(2),
        'precio_total' => 100,
        'id_estado' => 1
    ]);

    $response = $this->actingAs($ownerUser)->post(route('rentals.reject', $rental));
    $response->assertRedirect();

    $rental->refresh();
    expect($rental->id_estado)->toBe(6);
});
