<?php

use App\Models\User;
use App\Models\Cars;
use App\Jobs\ProcessCarImageJob;
use App\Jobs\CleanupRejectedOffersJob;
use App\Jobs\AuditCarPricesJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->admin->assignRole($role);
});

test('admin dashboard is accessible', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $response->assertStatus(200);
});

test('runJob executes artisan commands', function () {
    Artisan::shouldReceive('call')->with('cache:clear')->once();
    Artisan::shouldReceive('output')->andReturn('Cache cleared');

    $response = $this->actingAs($this->admin)->post(route('admin.run-job'), ['job' => 'clear-cache']);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('runJob dispatches process-image job', function () {
    Queue::fake();
    Cars::factory()->create();

    $response = $this->actingAs($this->admin)->post(route('admin.run-job'), ['job' => 'process-image']);

    $response->assertRedirect();
    $response->assertSessionHas('success'); // Ensure no error occurred
    Queue::assertPushed(ProcessCarImageJob::class);
});

test('runJob dispatches cleanup-offers job', function () {
    Queue::fake();

    $response = $this->actingAs($this->admin)->post(route('admin.run-job'), ['job' => 'cleanup-offers']);

    $response->assertRedirect();
    $response->assertSessionHas('success'); // Ensure no error occurred
    Queue::assertPushed(CleanupRejectedOffersJob::class);
});

test('runJob dispatches audit-prices job', function () {
    Queue::fake();

    $response = $this->actingAs($this->admin)->post(route('admin.run-job'), ['job' => 'audit-prices']);

    $response->assertRedirect();
    $response->assertSessionHas('success'); // Ensure no error occurred
    Queue::assertPushed(AuditCarPricesJob::class);
});

test('runJob handles unknown job', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.run-job'), ['job' => 'unknown']);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});
