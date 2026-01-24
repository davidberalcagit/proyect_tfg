<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use App\Livewire\Admin\BrandManager;
use App\Livewire\Admin\ModelManager;
use App\Livewire\Admin\FuelManager;
use App\Livewire\Admin\ColorManager;
use App\Livewire\Admin\GearManager;

beforeEach(function () {
    // Create admin role if it doesn't exist
    if (!Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin']);
    }
});

test('admin can access dashboard', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertSee('Admin Dashboard')
        ->assertSee('Marcas')
        ->assertSee('Modelos')
        ->assertSee('Combustibles')
        ->assertSee('Colores')
        ->assertSee('Marchas');
});

test('non-admin cannot access dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertStatus(403);
});

test('dashboard contains livewire components', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSeeLivewire(BrandManager::class)
        ->assertSeeLivewire(ModelManager::class)
        ->assertSeeLivewire(FuelManager::class)
        ->assertSeeLivewire(ColorManager::class)
        ->assertSeeLivewire(GearManager::class);
});
