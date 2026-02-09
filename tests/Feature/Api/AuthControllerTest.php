<?php

use App\Models\User;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

test('login returns token with valid credentials', function () {
    Queue::fake();
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson(route('api.login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['accessToken', 'token_type', 'user']);

    Queue::assertNotPushed(SendWelcomeEmailJob::class);
});

test('login fails with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson(route('api.login'), [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
             ->assertJson(['message' => 'Credenciales incorrectas']);
});

test('login validates input', function () {
    $response = $this->postJson(route('api.login'), []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email', 'password']);
});

test('logout deletes tokens', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.logout'));

    $response->assertStatus(200)
             ->assertJson(['message' => 'Sesión cerrada correctamente']);
});
