<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\BrandManager;
use App\Livewire\Admin\ModelManager;
use App\Livewire\Admin\FuelManager;
use App\Jobs\ProcessCarImageJob;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'admin']);
});

test('admin can access dashboard', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.dashboard');
    $response->assertSeeText('Admin Dashboard');
    $response->assertSeeText('Resumen del Sistema');
    $response->assertSeeText('Mantenimiento y Acciones');
    $response->assertSeeLivewire(AdminDashboard::class);
});

test('non admin cannot access dashboard', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('admin.dashboard'));
    $response->assertStatus(403);
});

test('dashboard can switch tabs and render components', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(AdminDashboard::class)
        // Initially shows brands
        ->assertSeeText('Gestión de Marcas')
        ->assertDontSeeText('Gestión de Modelos')
        // Switch to models
        ->call('setTab', 'models')
        ->assertSeeText('Gestión de Modelos')
        ->assertDontSeeText('Gestión de Marcas')
        // Switch to fuels
        ->call('setTab', 'fuels')
        ->assertSeeText('Gestión de Combustibles')
        ->assertDontSeeText('Gestión de Modelos');
});

test('admin can run artisan commands', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Artisan::shouldReceive('call')->with('cache:clear')->once();
    Artisan::shouldReceive('output')->andReturn('Cache cleared');

    $response = $this->actingAs($admin)->post(route('admin.run-job'), [
        'job' => 'clear-cache'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('admin can dispatch jobs', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    \App\Models\Cars::factory()->create();

    Queue::fake();

    $response = $this->actingAs($admin)->post(route('admin.run-job'), [
        'job' => 'process-image'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    Queue::assertPushed(ProcessCarImageJob::class);
});
