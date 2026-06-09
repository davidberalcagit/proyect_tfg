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
    Role::firstOrCreate(['name' => 'admin']);
});

test('model manager can create model', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $brand = Brands::factory()->create();

    Livewire::actingAs($admin)
        ->test(ModelManager::class)
        ->set('newModelBrandId', $brand->id)
        ->set('newModelName', 'New Model')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('car_models', ['nombre' => 'New Model', 'id_marca' => $brand->id]);
});

test('model manager can edit model', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $brand = Brands::factory()->create();
    $model = CarModels::factory()->create(['id_marca' => $brand->id, 'nombre' => 'Old Model']);

    Livewire::actingAs($admin)
        ->test(ModelManager::class)
        ->call('edit', $model->id)
        ->set('editingModelName', 'Updated Model')
        ->call('update');

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
