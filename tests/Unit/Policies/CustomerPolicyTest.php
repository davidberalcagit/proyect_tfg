<?php

use App\Models\Customers;
use App\Models\User;
use App\Policies\CustomerPolicy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->policy = new CustomerPolicy();

    // Setup permissions/roles
    Permission::firstOrCreate(['name' => 'view customers data']);
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'supervisor']);
});

test('viewAny requires permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('view customers data');

    expect($this->policy->viewAny($user))->toBeTrue();

    $userWithoutPermission = User::factory()->create();
    expect($this->policy->viewAny($userWithoutPermission))->toBeFalse();
});

test('view allows admin or supervisor', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    $customer = Customers::factory()->create();

    expect($this->policy->view($admin, $customer))->toBeTrue();
    expect($this->policy->view($supervisor, $customer))->toBeTrue();
});

test('view allows owner', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    expect($this->policy->view($user, $customer))->toBeTrue();
});

test('view denies others', function () {
    $user = User::factory()->create();
    $otherCustomer = Customers::factory()->create(); // Different user

    expect($this->policy->view($user, $otherCustomer))->toBeFalse();
});

test('update allows admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $customer = Customers::factory()->create();

    expect($this->policy->update($admin, $customer))->toBeTrue();
});

test('update allows owner', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    expect($this->policy->update($user, $customer))->toBeTrue();
});

test('update denies others', function () {
    $user = User::factory()->create();
    $otherCustomer = Customers::factory()->create();

    expect($this->policy->update($user, $otherCustomer))->toBeFalse();
});
