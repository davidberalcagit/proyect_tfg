<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('support can create user', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    $response = $this->actingAs($support)->post(route('support.users.store'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'individual',
    ]);

    $response->assertRedirect(route('support.users.index'));
    $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    $this->assertDatabaseHas('customers', ['nombre_contacto' => 'New User']);
});

test('support can delete user', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    $userToDelete = User::factory()->create();
    $userToDelete->assignRole('individual');

    $response = $this->actingAs($support)->delete(route('support.users.destroy', $userToDelete));

    $response->assertRedirect(route('support.users.index'));
    $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
});

test('support cannot delete themselves', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    $response = $this->actingAs($support)->delete(route('support.users.destroy', $support));

    // La Policy impide esto devolviendo 403
    $response->assertStatus(403);

    $this->assertDatabaseHas('users', ['id' => $support->id]);
});

test('support cannot edit themselves', function () {
    $support = User::factory()->create();
    $support->assignRole('soporte');

    // Intentar acceder a la vista de edición
    $response = $this->actingAs($support)->get(route('support.users.edit', $support));
    $response->assertRedirect(route('support.users.index'));
    $response->assertSessionHas('error');

    // Intentar actualizar
    $response = $this->actingAs($support)->put(route('support.users.update', $support), [
        'name' => 'New Name',
        'email' => $support->email,
        'role' => 'soporte'
    ]);

    $response->assertRedirect(route('support.users.index'));
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('users', ['id' => $support->id, 'name' => $support->name]); // Nombre original
});
