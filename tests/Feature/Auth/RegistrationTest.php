<?php

use Database\Seeders\EntityTypesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration screen can be rendered', function () {
    if (! Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration support is not enabled.');
    }

    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    if (! Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration support is not enabled.');
    }

    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(EntityTypesSeeder::class);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        'type' => 'individual',
        'telefono' => '600123456',
        'id_entidad' => 1,
        'dni' => '12345678Z',
        'fecha_nacimiento' => '1990-01-01',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
