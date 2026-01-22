<?php

use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('admin can delete any user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $user = User::factory()->create();

    expect($admin->can('delete', $user))->toBeTrue();
});

test('support can delete user but not admin', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');
    $user = User::factory()->create();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    expect($support->can('delete', $user))->toBeTrue();
    expect($support->can('delete', $admin))->toBeFalse();
});

test('user can delete themselves', function () {
    $user = User::factory()->create();
    expect($user->can('delete', $user))->toBeTrue();
});
