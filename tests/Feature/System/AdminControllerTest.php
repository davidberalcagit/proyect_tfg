<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('admin dashboard is accessible by admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
});

test('admin dashboard is not accessible by regular user', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertStatus(403);
});

test('admin can run jobs', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Mockear Artisan para no ejecutar realmente el comando y tardar
    Artisan::shouldReceive('call')->with('cache:clear')->once();
    Artisan::shouldReceive('output')->andReturn('Cache cleared');

    $response = $this->actingAs($admin)->post(route('admin.run-job'), [
        'job' => 'clear-cache'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});
