<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->seed(\Database\Seeders\StatusesSeeder::class);
});

test('individual role can create offer because it has offers permissions', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $seller = Customers::factory()->create();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id, 'id_estado' => 1]);

    $this->assertTrue($user->can('create', [\App\Models\Offer::class, $car]));
});

test('dealership role can create offer because it has offers permissions', function () {
    $user = User::factory()->create();
    $user->assignRole('dealership');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $seller = Customers::factory()->create();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id, 'id_estado' => 1]);

    $this->assertTrue($user->can('create', [\App\Models\Offer::class, $car]));
});

test('user without offers permissions cannot create offer', function () {
    // Creamos un rol restringido que solo puede comprar, pero no tiene el permiso explícito de "offers"
    $role = Role::create(['name' => 'restricted_user']);
    $role->givePermissionTo('buy cars');

    $user = User::factory()->create();
    $user->assignRole('restricted_user');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $seller = Customers::factory()->create();
    $car = Cars::factory()->create(['id_vendedor' => $seller->id, 'id_estado' => 1]);

    // La política debería bloquearlo porque le faltan 'offers for individuals' u 'offers for companies'
    $this->assertFalse($user->can('create', [\App\Models\Offer::class, $car]));
});
