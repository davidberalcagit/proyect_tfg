<?php

use App\Models\User;
use App\Models\Cars;
use App\Events\CarRejected;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;

beforeEach(function () {
    $this->supervisor = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'supervisor']);
    $this->supervisor->assignRole($role);
});

test('supervisor dashboard is accessible', function () {
    $response = $this->actingAs($this->supervisor)->get(route('supervisor.dashboard'));
    $response->assertStatus(200);
});

test('approveCar calls artisan command', function () {
    Artisan::shouldReceive('call')
        ->once()
        ->with('cars:approve', ['car_id' => 1])
        ->andReturn(0);

    // Corrected route name
    $response = $this->actingAs($this->supervisor)->post(route('supervisor.approve', 1));

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('rejectCar updates status and dispatches event', function () {
    Event::fake();
    $car = Cars::factory()->create(['id_estado' => 4]);

    // Corrected route name
    $response = $this->actingAs($this->supervisor)->post(route('supervisor.reject', $car->id), [
        'reason' => 'Bad quality'
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('cars', ['id' => $car->id, 'id_estado' => 5, 'rejection_reason' => 'Bad quality']);
    Event::assertDispatched(CarRejected::class);
});

test('downloadReport generates pdf', function () {
    Pdf::shouldReceive('loadView')->andReturnSelf();
    Pdf::shouldReceive('download')->andReturn(response('PDF Content'));

    // Corrected route name
    $response = $this->actingAs($this->supervisor)->get(route('supervisor.report'));

    $response->assertStatus(200);
});
