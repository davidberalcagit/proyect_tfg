<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\ModelManager;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'admin']);
});

test('model manager can create model', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $brand = Brands::factory()->create();

    Livewire::actingAs($admin)
        ->test(ModelManager::class)
        ->set('id_marca', $brand->id)
        ->set('nombre', 'New Model')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('car_models', ['nombre' => 'New Model', 'id_marca' => $brand->id]);
});

test('model manager validates required fields', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(ModelManager::class)
        ->set('nombre', '')
        ->call('store')
        ->assertHasErrors(['nombre' => 'required']);
});

test('model manager can edit model', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $brand = Brands::factory()->create();
    $model = CarModels::factory()->create(['id_marca' => $brand->id, 'nombre' => 'Old Model']);

    Livewire::actingAs($admin)
        ->test(ModelManager::class)
        ->call('edit', $model->id)
        ->set('nombre', 'Updated Model') // Corrected property
        ->call('store'); // Uses store for update

    $this->assertDatabaseHas('car_models', ['id' => $model->id, 'nombre' => 'Updated Model']);
});

test('model manager can delete model', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $brand = Brands::factory()->create();
    $model = CarModels::factory()->create(['id_marca' => $brand->id]);

    Livewire::actingAs($admin)
        ->test(ModelManager::class)
        ->call('delete', $model->id);

    $this->assertDatabaseMissing('car_models', ['id' => $model->id]);
});
