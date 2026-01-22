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
}
