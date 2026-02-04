<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'individual']);
    Role::create(['name' => 'supervisor']);
});

test('admin dashboard loads', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
         ->get(route('admin.dashboard'))
         ->assertStatus(200);
});

test('admin dashboard forbids non-admin', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');

    $this->actingAs($user)
         ->get(route('admin.dashboard'))
         ->assertStatus(403);
});

test('admin dashboard forbids supervisor', function () {
    $user = User::factory()->create();
    $user->assignRole('supervisor');

    $this->actingAs($user)
         ->get(route('admin.dashboard'))
         ->assertStatus(403);
});

test('admin can run jobs', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
         ->post(route('admin.run-job'), ['job' => 'cleanup-images'])
         ->assertRedirect();
});
