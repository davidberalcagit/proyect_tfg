<?php

use App\Models\User;
use Database\Seeders\EntityTypesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an individual user can register successfully', function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(EntityTypesSeeder::class);

    $response = $this->post(route('register'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'type' => 'individual',
        'telefono' => '600123123',
        'id_entidad' => '1',
        'dni' => '12345678Z',
        'fecha_nacimiento' => '1995-01-01',
        'terms' => 'on',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);

    $this->assertDatabaseHas('customers', [
        'nombre_contacto' => 'John Doe',
        'telefono' => '600123123',
    ]);

    $this->assertDatabaseHas('individuals', [
        'dni' => '12345678Z',
        'fecha_nacimiento' => '1995-01-01',
    ]);

    $user = User::where('email', 'john@example.com')->first();

    expect($user->customer)->not->toBeNull();
    expect($user->customer->individual)->not->toBeNull();
});
