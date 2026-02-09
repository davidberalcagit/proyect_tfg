<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $individualRole = Role::firstOrCreate(['name' => 'individual']);

    Permission::firstOrCreate(['name' => 'buy cars']);
    Permission::firstOrCreate(['name' => 'view customers data']);

    $individualRole->givePermissionTo('buy cars');
    $adminRole->givePermissionTo('view customers data');
});

test('offer policy authorization', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $otherUser = User::factory()->create();
    $otherUser->assignRole('individual');
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 1
    ]);

    $offer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_comprador' => $otherCustomer->id,
        'id_vendedor' => $customer->id,
        'cantidad' => 5000,
        'estado' => 'pending'
    ]);

    expect($user->can('viewAny', Offer::class))->toBeTrue();

    expect($user->can('view', $offer))->toBeTrue();
    expect($otherUser->can('view', $offer))->toBeTrue();

    expect($otherUser->can('create', [Offer::class, $car]))->toBeTrue();

    expect($user->can('update', $offer))->toBeTrue();

    expect($user->can('delete', $offer))->toBeFalse();
    expect($otherUser->can('delete', $offer))->toBeTrue();
});

test('customer policy authorization', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    expect($admin->can('viewAny', Customers::class))->toBeTrue();

    expect($user->can('view', $customer))->toBeTrue();

    expect($user->can('update', $customer))->toBeTrue();
});

test('user policy authorization', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user = User::factory()->create();

    expect($admin->can('viewAny', User::class))->toBeTrue();
    expect($user->can('viewAny', User::class))->toBeFalse();

    expect($admin->can('view', $user))->toBeTrue();
    expect($user->can('view', $user))->toBeTrue();

    expect($admin->can('create', User::class))->toBeTrue();

    expect($admin->can('update', $user))->toBeTrue();
    expect($user->can('update', $user))->toBeTrue();

    expect($admin->can('delete', $user))->toBeTrue();
    expect($user->can('delete', $admin))->toBeFalse();
});
