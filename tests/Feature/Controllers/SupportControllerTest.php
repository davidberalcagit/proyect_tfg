<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'soporte']);
    Role::create(['name' => 'individual']);

    DB::table('entity_types')->insertOrIgnore([
        ['id' => 1, 'nombre' => 'Particular'],
        ['id' => 2, 'nombre' => 'Concesionario'],
    ]);
});

test('support index lists users', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    User::factory()->count(3)->create();

    $this->actingAs($support)
         ->get(route('support.users.index'))
         ->assertStatus(200);
});

test('support create page loads', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    $this->actingAs($support)
         ->get(route('support.users.create'))
         ->assertStatus(200);
});

test('support can create user', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    $this->actingAs($support)
         ->post(route('support.users.store'), [
             'name' => 'New User',
             'email' => 'new@example.com',
             'password' => 'password',
             'password_confirmation' => 'password',
             'role' => 'individual'
         ])
         ->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
});

test('support show page loads', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');
    $user = User::factory()->create();

    $this->actingAs($support)
         ->get(route('support.users.show', $user->id))
         ->assertStatus(200);
});

test('support edit page loads', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');
    $user = User::factory()->create();

    $this->actingAs($support)
         ->get(route('support.users.edit', $user->id))
         ->assertStatus(200);
});

test('support can edit user', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    $user = User::factory()->create(['name' => 'Old Name']);

    $this->actingAs($support)
         ->put(route('support.users.update', $user->id), [
             'name' => 'New Name',
             'email' => $user->email,
             'role' => 'individual'
         ])
         ->assertRedirect();

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
});

test('support can delete user', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    $user = User::factory()->create();

    $this->actingAs($support)
         ->delete(route('support.users.destroy', $user->id))
         ->assertRedirect();

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
