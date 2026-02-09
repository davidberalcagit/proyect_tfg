<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\Customers;
use App\Models\Dealerships;
use App\Models\EntityType;
use App\Models\Individuals;
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

test('validates required fields', function () {
    $this->expectException(ValidationException::class);

    $this->action->create([]);
});

test('creates individual user successfully', function () {
    $input = [
        'name' => 'John Doe',
        'contact_name' => 'John Contact',
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
    $this->assertDatabaseHas('customers', ['telefono' => '123456789', 'nombre_contacto' => 'John Contact']);
    $this->assertDatabaseHas('individuals', ['dni' => '12345678A']);

    expect($user->hasRole('individual'))->toBeTrue();
});

test('creates dealership user successfully', function () {
    $input = [
        'name' => 'Jane Doe',
        'contact_name' => 'Jane Contact',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'type' => 'dealership',
        'telefono' => '987654321',
        'id_entidad' => $this->entityType->id,
        'nombre_empresa' => 'Auto World',
        'nif' => 'B12345678',
        'direccion' => 'Main St 123',
        'terms' => 'on',
    ];

    $user = $this->action->create($input);

    expect($user)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    $this->assertDatabaseHas('dealerships', ['nif' => 'B12345678']);

    $customer = Customers::where('telefono', '987654321')->first();
    expect($customer->dealership_id)->not->toBeNull();
    expect($customer->nombre_contacto)->toBe('Jane Contact');

    expect($user->hasRole('dealership'))->toBeTrue();
});

test('creates admin user successfully', function () {
    $input = [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'type' => 'admin',
        'terms' => 'on',
    ];

    $user = $this->action->create($input);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->hasRole('admin'))->toBeTrue();
});
