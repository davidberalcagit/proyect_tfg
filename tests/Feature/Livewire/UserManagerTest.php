<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\UserManager;
use App\Models\Customers;
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

test('user manager cannot delete self', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(UserManager::class)
        ->call('delete', $admin->id);

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('user manager can search users', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    User::factory()->create(['name' => 'Alice']);
    User::factory()->create(['name' => 'Bob']);

    Livewire::actingAs($admin)
        ->test(UserManager::class)
        ->set('search', 'Alice')
        ->assertSee('Alice')
        ->assertDontSee('Bob');
});

test('user manager can sort users', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $userA = User::factory()->create(['name' => 'AAA']);
    $userZ = User::factory()->create(['name' => 'ZZZ']);

    Livewire::actingAs($admin)
        ->test(UserManager::class)
        ->call('sortBy', 'name') // Ascending
        ->assertSeeInOrder(['AAA', 'ZZZ'])
        ->call('sortBy', 'name') // Descending
        ->assertSeeInOrder(['ZZZ', 'AAA']);
});

test('user manager can sort by seller name', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user1 = User::factory()->create();
    Customers::factory()->create(['id_usuario' => $user1->id, 'nombre_contacto' => 'AAA Seller']);

    $user2 = User::factory()->create();
    Customers::factory()->create(['id_usuario' => $user2->id, 'nombre_contacto' => 'ZZZ Seller']);

    Livewire::actingAs($admin)
        ->test(UserManager::class)
        ->call('sortBy', 'seller_name')
        ->assertSeeInOrder(['AAA Seller', 'ZZZ Seller'])
        ->call('sortBy', 'seller_name')
        ->assertSeeInOrder(['ZZZ Seller', 'AAA Seller']);
});
