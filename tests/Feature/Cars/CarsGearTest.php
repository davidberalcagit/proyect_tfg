<?php

use App\Models\Customers;
use App\Models\Gears;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('car creation page contains gears', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $gear = Gears::factory()->create(['tipo' => 'Super-Manual']);

    $response = $this->actingAs($user)->get(route('cars.create'));

    $response->assertStatus(200);
    $response->assertSee('Super-Manual');
});
