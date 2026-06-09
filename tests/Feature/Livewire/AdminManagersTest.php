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
    Role::firstOrCreate(['name' => 'admin']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

test('brand manager can create brand', function () {
    Livewire::actingAs($this->admin)
        ->test(BrandManager::class)
        ->set('newBrandName', 'New Brand')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('brands', ['nombre' => 'New Brand']);
});

test('brand manager can edit brand', function () {
    $brand = Brands::factory()->create(['nombre' => 'Old Brand']);

    Livewire::actingAs($this->admin)
        ->test(BrandManager::class)
        ->call('edit', $brand->id)
        ->set('editingBrandName', 'Updated Brand')
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('brands', ['id' => $brand->id, 'nombre' => 'Updated Brand']);
});

test('brand manager can edit multiple times', function () {
    $brand = Brands::factory()->create(['nombre' => 'Initial Name']);

    Livewire::actingAs($this->admin)
        ->test(BrandManager::class)
        // First Edit
        ->call('edit', $brand->id)
        ->set('editingBrandName', 'First Edit')
        ->call('update')
        ->assertHasNoErrors()
        ->assertSee('First Edit')

        // Second Edit
        ->call('edit', $brand->id)
        ->set('editingBrandName', 'Second Edit')
        ->call('update')
        ->assertHasNoErrors()
        ->assertSee('Second Edit');

    $this->assertDatabaseHas('brands', ['id' => $brand->id, 'nombre' => 'Second Edit']);
});


test('color manager can create color', function () {
    Livewire::actingAs($this->admin)
        ->test(ColorManager::class)
        ->set('newColorName', 'New Color')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('colors', ['nombre' => 'New Color']);
});

test('color manager can edit color', function () {
    $color = Color::factory()->create(['nombre' => 'Old Color']);

    Livewire::actingAs($this->admin)
        ->test(ColorManager::class)
        ->call('edit', $color->id)
        ->set('editingColorName', 'Updated Color')
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('colors', ['id' => $color->id, 'nombre' => 'Updated Color']);
});

test('fuel manager can create fuel', function () {
    Livewire::actingAs($this->admin)
        ->test(FuelManager::class)
        ->set('newFuelName', 'New Fuel')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('fuels', ['nombre' => 'New Fuel']);
});

test('fuel manager can edit fuel', function () {
    $fuel = Fuels::factory()->create(['nombre' => 'Old Fuel']);

    Livewire::actingAs($this->admin)
        ->test(FuelManager::class)
        ->call('edit', $fuel->id)
        ->set('editingFuelName', 'Updated Fuel')
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('fuels', ['id' => $fuel->id, 'nombre' => 'Updated Fuel']);
});

test('gear manager can create gear', function () {
    Livewire::actingAs($this->admin)
        ->test(GearManager::class)
        ->set('newGearType', 'New Gear')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('gears', ['tipo' => 'New Gear']);
});

test('gear manager can edit gear', function () {
    $gear = Gears::factory()->create(['tipo' => 'Old Gear']);

    Livewire::actingAs($this->admin)
        ->test(GearManager::class)
        ->call('edit', $gear->id)
        ->set('editingGearType', 'Updated Gear')
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('gears', ['id' => $gear->id, 'tipo' => 'Updated Gear']);
});
