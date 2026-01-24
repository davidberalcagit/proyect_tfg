<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\Customers;
use App\Models\EntityType;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->action = new CreateNewUser();
    $this->entityType = EntityType::factory()->create();

    Role::firstOrCreate(['name' => 'individual']);
    Role::firstOrCreate(['name' => 'dealership']);
    Role::firstOrCreate(['name' => 'admin']);
});

test('creates individual user successfully', function () {
    $input = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'type' => 'individual',
        'telefono' => '123456789',
        'id_entidad' => $this->entityType->id,
        'dni' => '12345678A',
        'fecha_nacimiento' => '1990-01-01',
        'terms' => 'on',
    ];

    $user = $this->action->create($input);

    expect($user)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    expect($user->hasRole('individual'))->toBeTrue();
});

test('validates required fields', function () {
    $this->expectException(ValidationException::class);
    $this->action->create([]);
});
