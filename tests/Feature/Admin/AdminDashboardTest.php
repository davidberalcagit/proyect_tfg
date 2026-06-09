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
use App\Livewire\Admin\ColorManager;
use App\Livewire\Admin\GearManager;
use App\Jobs\ProcessCarImageJob;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
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

test('dashboard renders correct child components based on active tab', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Comprobamos la pestaña por defecto y cambiamos a todas las demás
    Livewire::actingAs($admin)
        ->test(AdminDashboard::class)
        // Por defecto debe ser brands
        ->assertSet('activeTab', 'brands')
        ->assertSeeLivewire(BrandManager::class)
        ->assertDontSeeLivewire(ModelManager::class)

        // Cambiar a models
        ->call('setTab', 'models')
        ->assertSet('activeTab', 'models')
        ->assertSeeLivewire(ModelManager::class)
        ->assertDontSeeLivewire(BrandManager::class)

        // Cambiar a fuels
        ->call('setTab', 'fuels')
        ->assertSet('activeTab', 'fuels')
        ->assertSeeLivewire(FuelManager::class)
        ->assertDontSeeLivewire(ModelManager::class)

        // Cambiar a colors
        ->call('setTab', 'colors')
        ->assertSet('activeTab', 'colors')
        ->assertSeeLivewire(ColorManager::class)
        ->assertDontSeeLivewire(FuelManager::class)

        // Cambiar a gears
        ->call('setTab', 'gears')
        ->assertSet('activeTab', 'gears')
        ->assertSeeLivewire(GearManager::class)
        ->assertDontSeeLivewire(ColorManager::class);
});

test('dashboard view contains wire loading state', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(AdminDashboard::class)
        ->assertSeeHtml('wire:loading')
        ->assertSeeHtml('animate-pulse');
});

test('dashboard view contains navigation buttons for all tabs', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(AdminDashboard::class)
        ->assertSeeHtml('wire:click="setTab(\'brands\')"')
        ->assertSeeHtml('wire:click="setTab(\'models\')"')
        ->assertSeeHtml('wire:click="setTab(\'fuels\')"')
        ->assertSeeHtml('wire:click="setTab(\'colors\')"')
        ->assertSeeHtml('wire:click="setTab(\'gears\')"');
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
