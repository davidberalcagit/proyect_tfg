<?php

namespace Tests\Feature\System;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_support_can_create_user()
    {
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
    }

    public function test_support_can_delete_user()
    {
        $support = User::factory()->create();
        $support->assignRole('soporte');

        $userToDelete = User::factory()->create();
        $userToDelete->assignRole('individual');

        $response = $this->actingAs($support)->delete(route('support.users.destroy', $userToDelete));

        $response->assertRedirect(route('support.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_support_cannot_delete_themselves()
    {
        $support = User::factory()->create();
        $support->assignRole('soporte');

        $response = $this->actingAs($support)->delete(route('support.users.destroy', $support));

        // La Policy impide esto devolviendo 403
        $response->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $support->id]);
    }

    public function test_support_cannot_edit_themselves()
    {
        $support = User::factory()->create();
        $support->assignRole('soporte');

        // Intentar acceder a la vista de edición
        // Aquí el controlador redirige, porque la policy update permite editarse a sí mismo (normalmente)
        // Pero en UserPolicy::update para soporte pusimos "return true", así que pasa la policy.
        // Y el controlador bloquea.

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
    }
}
