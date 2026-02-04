<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\UserManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'admin']);
});

test('user manager renders', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(UserManager::class)
        ->assertStatus(200);
});

test('user manager can delete user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $userToDelete = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(UserManager::class)
        ->call('delete', $userToDelete->id);

    $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
});
