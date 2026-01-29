<?php

namespace Tests\Feature\Policies;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'supervisor']);
    Role::create(['name' => 'individual']);
});

// --- Customer Policy ---

test('admin can view any customer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $customer = Customers::factory()->create();

    expect($admin->can('view', $customer))->toBeTrue();
});

test('user can view own customer profile', function () {
    $user = User::factory()->create();
    $user->assignRole('individual'); // Assign role
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    expect($user->can('view', $customer))->toBeTrue();
});

test('user cannot view other customer profile', function () {
    $user = User::factory()->create();
    $user->assignRole('individual'); // Assign role
    $otherCustomer = Customers::factory()->create();

    expect($user->can('view', $otherCustomer))->toBeFalse();
});

// --- Cars Policy ---

test('owner can update own car', function () {
    $user = User::factory()->create();
    $user->assignRole('individual'); // Assign role to ensure permissions
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    // Car must be in pending state (4) to be editable by owner
    $car = Cars::factory()->create(['id_vendedor' => $customer->id, 'id_estado' => 4]);

    // Reload user to ensure relations/permissions are fresh
    $user = $user->fresh();

    expect($user->can('update', $car))->toBeTrue();
});

test('other user cannot update car', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $otherCustomer = Customers::factory()->create();
    $car = Cars::factory()->create(['id_vendedor' => $otherCustomer->id]);

    expect($user->can('update', $car))->toBeFalse();
});

test('admin can delete any car', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $car = Cars::factory()->create();

    expect($admin->can('delete', $car))->toBeTrue();
});
