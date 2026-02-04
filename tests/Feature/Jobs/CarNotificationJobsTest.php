<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendCarApprovedNotificationJob;
use App\Jobs\SendCarRejectedNotificationJob;
use App\Mail\CarApproved;
use App\Mail\CarRejected;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery;

uses(RefreshDatabase::class);

test('send car approved notification job sends email', function () {
    Mail::fake();

    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    $car = Cars::factory()->create(['id_vendedor' => $customer->id]);

    $job = new SendCarApprovedNotificationJob($car);
    $job->handle();

    if (in_array(\Illuminate\Contracts\Queue\ShouldQueue::class, class_implements(CarApproved::class))) {
        Mail::assertQueued(CarApproved::class);
    } else {
        Mail::assertSent(CarApproved::class);
    }
});

test('send car approved notification logs warning if user missing', function () {
    Mail::fake();
    Log::spy();

    $car = Mockery::mock(Cars::class);
    $car->shouldReceive('getAttribute')->with('id')->andReturn(1);
    $car->shouldReceive('load')->with('vendedor.user')->andReturnSelf();
    $car->shouldReceive('offsetExists')->andReturn(true);
    $car->shouldReceive('offsetGet')->with('vendedor')->andReturn(null);

    $customer = Mockery::mock(Customers::class);
    $customer->shouldReceive('getAttribute')->with('user')->andReturn(null);
    $customer->shouldReceive('offsetExists')->andReturn(true);

    $car->shouldReceive('getAttribute')->with('vendedor')->andReturn($customer);

    $job = new SendCarApprovedNotificationJob($car);
    $job->handle();

    Mail::assertNothingSent();
    Log::shouldHaveReceived('warning');
});

test('send car rejected notification job sends email', function () {
    Mail::fake();

    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    $car = Cars::factory()->create(['id_vendedor' => $customer->id]);

    $job = new SendCarRejectedNotificationJob($car, 'Reason for rejection');
    $job->handle();

    if (in_array(\Illuminate\Contracts\Queue\ShouldQueue::class, class_implements(CarRejected::class))) {
        Mail::assertQueued(CarRejected::class);
    } else {
        Mail::assertSent(CarRejected::class);
    }
});

test('send car rejected notification logs warning if user missing', function () {
    Mail::fake();
    Log::spy();

    $car = Mockery::mock(Cars::class);
    $car->shouldReceive('getAttribute')->with('id')->andReturn(1);
    $car->shouldReceive('load')->with('vendedor.user')->andReturnSelf();
    $car->shouldReceive('offsetExists')->andReturn(true);

    $customer = Mockery::mock(Customers::class);
    $customer->shouldReceive('getAttribute')->with('user')->andReturn(null);
    $customer->shouldReceive('offsetExists')->andReturn(true);

    $car->shouldReceive('getAttribute')->with('vendedor')->andReturn($customer);

    $job = new SendCarRejectedNotificationJob($car, 'Reason');
    $job->handle();

    Mail::assertNothingSent();
    Log::shouldHaveReceived('warning');
});
