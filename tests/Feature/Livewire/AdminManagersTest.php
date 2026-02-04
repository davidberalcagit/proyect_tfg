<?php

use App\Livewire\Admin\BrandManager;
use App\Livewire\Admin\ColorManager;
use App\Livewire\Admin\FuelManager;
use App\Livewire\Admin\GearManager;
use App\Models\Brands;
use App\Models\Color;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'admin']);
});

// --- BrandManager Tests ---
test('brand manager can create brand', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(BrandManager::class)
        ->set('nombre', 'New Brand')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('brands', ['nombre' => 'New Brand']);
});

test('brand manager validates required name', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(BrandManager::class)
        ->set('nombre', '')
        ->call('store')
        ->assertHasErrors(['nombre' => 'required']);
});

test('brand manager can edit brand', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $brand = Brands::factory()->create(['nombre' => 'Old Brand']);

    Livewire::actingAs($admin)
        ->test(BrandManager::class)
        ->call('edit', $brand->id)
        ->set('editingNombre', 'Updated Brand')
        ->call('update');

    $this->assertDatabaseHas('brands', ['id' => $brand->id, 'nombre' => 'Updated Brand']);
});

test('brand manager can delete brand', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $brand = Brands::factory()->create();

    Livewire::actingAs($admin)
        ->test(BrandManager::class)
        ->call('delete', $brand->id);

    $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
});

// --- GearManager Tests ---
test('gear manager can create gear', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(GearManager::class)
        ->set('tipo', 'Automatic')
        ->call('store');

    $this->assertDatabaseHas('gears', ['tipo' => 'Automatic']);
});

test('gear manager validates unique gear', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Gears::create(['tipo' => 'Manual']);

    Livewire::actingAs($admin)
        ->test(GearManager::class)
        ->set('tipo', 'Manual')
        ->call('store')
        ->assertHasErrors(['tipo' => 'unique']);
});

test('gear manager can edit gear', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $gear = Gears::create(['tipo' => 'Manual']);

    Livewire::actingAs($admin)
        ->test(GearManager::class)
        ->call('edit', $gear->id)
        ->set('tipo', 'CVT')
        ->call('store');

    $this->assertDatabaseHas('gears', ['id' => $gear->id, 'tipo' => 'CVT']);
});

test('gear manager can delete gear', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $gear = Gears::create(['tipo' => 'Manual']);

    Livewire::actingAs($admin)
        ->test(GearManager::class)
        ->call('delete', $gear->id);

    $this->assertDatabaseMissing('gears', ['id' => $gear->id]);
});

// --- FuelManager Tests ---
test('fuel manager can create fuel', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(FuelManager::class)
        ->set('nombre', 'Electric')
        ->call('store');

    $this->assertDatabaseHas('fuels', ['nombre' => 'Electric']);
});

test('fuel manager validates required name', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(FuelManager::class)
        ->set('nombre', '')
        ->call('store')
        ->assertHasErrors(['nombre' => 'required']);
});

test('fuel manager can edit fuel', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $fuel = Fuels::factory()->create(['nombre' => 'Old Fuel']);

    Livewire::actingAs($admin)
        ->test(FuelManager::class)
        ->call('edit', $fuel->id)
        ->set('nombre', 'Updated Fuel')
        ->call('store');

    $this->assertDatabaseHas('fuels', ['id' => $fuel->id, 'nombre' => 'Updated Fuel']);
});

test('fuel manager can delete fuel', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $fuel = Fuels::factory()->create();

    Livewire::actingAs($admin)
        ->test(FuelManager::class)
        ->call('delete', $fuel->id);

    $this->assertDatabaseMissing('fuels', ['id' => $fuel->id]);
});

// --- ColorManager Tests ---
test('color manager can create color', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(ColorManager::class)
        ->set('nombre', 'Red')
        ->call('store');

    $this->assertDatabaseHas('colors', ['nombre' => 'Red']);
});

test('color manager validates unique color', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Color::create(['nombre' => 'Blue']);

    Livewire::actingAs($admin)
        ->test(ColorManager::class)
        ->set('nombre', 'Blue')
        ->call('store')
        ->assertHasErrors(['nombre' => 'unique']);
});

test('color manager can edit color', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $color = Color::factory()->create(['nombre' => 'Old Color']);

    Livewire::actingAs($admin)
        ->test(ColorManager::class)
        ->call('edit', $color->id)
        ->set('nombre', 'Updated Color')
        ->call('store');

    $this->assertDatabaseHas('colors', ['id' => $color->id, 'nombre' => 'Updated Color']);
});

test('color manager can delete color', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $color = Color::factory()->create();

    Livewire::actingAs($admin)
        ->test(ColorManager::class)
        ->call('delete', $color->id);

    $this->assertDatabaseMissing('colors', ['id' => $color->id]);
});
