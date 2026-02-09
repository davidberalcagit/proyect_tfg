<?php

use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('profile page is displayed', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
});

test('profile information can be updated', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'New Name',
        'email' => $user->email,
        'type' => 'particular',
    ]);

    $response->assertRedirect(route('profile.edit'));
    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
});

test('user can delete account', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $response = $this->actingAs($user)->delete(route('profile.destroy'), [
        'password' => 'password',
    ]);

    $response->assertRedirect('/');
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
