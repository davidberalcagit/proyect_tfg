<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('api user can get their profile', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/user');

    $response->assertStatus(200)
             ->assertJson(['id' => $user->id, 'email' => $user->email]);
});

test('unauthenticated user cannot access protected api routes', function () {
    $response = $this->getJson('/api/user');

    $response->assertStatus(401);
});

// Si tienes rutas de login/register específicas para API, añádelas aquí.
// Por defecto Jetstream usa cookies para SPA o tokens manuales.
// Asumimos que la autenticación funciona vía Sanctum.
