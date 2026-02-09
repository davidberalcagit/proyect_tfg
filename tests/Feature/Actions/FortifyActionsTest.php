<?php

namespace Tests\Feature\Actions;

use App\Actions\Fortify\CreateNewUser;
use App\Models\EntityType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'individual']);
    EntityType::factory()->create(['id' => 1, 'nombre' => 'Particular']);
});

test('create new user action creates user and customer', function () {
    $action = new CreateNewUser();

    $user = $action->create([
        'name' => 'Test User',
        'contact_name' => 'Test Contact', // Added
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'type' => 'individual',
        'terms' => 'on',
        'telefono' => '123456789',
        'id_entidad' => 1,
        'dni' => '12345678A',
        'fecha_nacimiento' => '1990-01-01',
    ]);

    expect($user)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $this->assertDatabaseHas('customers', ['id_usuario' => $user->id, 'nombre_contacto' => 'Test Contact']);
    expect($user->hasRole('individual'))->toBeTrue();
});

test('create new user validates input', function () {
    $action = new CreateNewUser();

    $action->create([
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'short',
        'password_confirmation' => 'mismatch',
        'type' => 'individual',
    ]);
})->throws(ValidationException::class);
