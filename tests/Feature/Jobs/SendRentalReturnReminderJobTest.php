<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendRentalReturnReminderJob;
use App\Mail\RentalReturnReminder;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Customers;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('rental_statuses')->insertOrIgnore([
        ['id' => 3, 'nombre' => 'Activo'],
    ]);

    $this->brand = Brands::factory()->create();
    $this->model = CarModels::factory()->create(['id_marca' => $this->brand->id]);
    $this->fuel = Fuels::factory()->create();
    $this->gear = Gears::factory()->create();
    $this->color = Color::factory()->create();
    $this->listingType = ListingType::factory()->create();
});

test('send rental return reminder job sends email', function () {
    Mail::fake();

    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->model->id,
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
    ]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => now()->subDays(5),
        'fecha_fin' => now()->addDay(),
        'precio_total' => 100,
        'id_estado' => 3
    ]);

    $job = new SendRentalReturnReminderJob($rental);
    $job->handle();

    Mail::assertQueued(RentalReturnReminder::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('send rental return reminder job logs warning if user not found', function () {
    Mail::fake();
    Log::spy();

    $rental = Mockery::mock(Rental::class);
    $rental->shouldReceive('getAttribute')->with('id')->andReturn(1);

    $customer = Mockery::mock(Customers::class);
    $customer->shouldReceive('getAttribute')->with('user')->andReturn(null);

    $rental->shouldReceive('getAttribute')->with('customer')->andReturn($customer);

    $job = new SendRentalReturnReminderJob($rental);
    $job->handle();

    Mail::assertNothingQueued();
    Log::shouldHaveReceived('warning');
});

test('send rental return reminder job logs error on exception', function () {
    Log::spy();
    Mail::shouldReceive('to')->andThrow(new \Exception('Mail error'));

    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->model->id,
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'id_color' => $this->color->id,
        'id_listing_type' => $this->listingType->id,
    ]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => now()->subDays(5),
        'fecha_fin' => now()->addDay(),
        'precio_total' => 100,
        'id_estado' => 3
    ]);

    $job = new SendRentalReturnReminderJob($rental);

    try {
        $job->handle();
    } catch (\Exception $e) {
        // Expected
    }

    Log::shouldHaveReceived('error');
});
