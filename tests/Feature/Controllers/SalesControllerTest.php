<?php

namespace Tests\Feature\Controllers;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'individual']);
});

test('sales index displays transactions', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    Sales::factory()->create(['id_comprador' => $customer->id]);

    $this->actingAs($user)
         ->get(route('sales.index'))
         ->assertStatus(200)
         ->assertSee('My Transactions');
});

test('sales export downloads csv', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    Sales::factory()->create(['id_comprador' => $customer->id]);

    $response = $this->actingAs($user)
         ->get(route('sales.export'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('sales receipt download', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $sale = Sales::factory()->create(['id_comprador' => $customer->id]);

    $this->actingAs($user)
         ->get(route('sales.receipt', $sale->id))
         ->assertStatus(200);
});

test('sales receipt download forbids unauthorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $userCustomer = Customers::factory()->create(['id_usuario' => $user->id]);

    $otherUser = User::factory()->create();
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id]);

    $thirdUser = User::factory()->create();
    $thirdCustomer = Customers::factory()->create(['id_usuario' => $thirdUser->id]);

    // Create sale between otherCustomer and thirdCustomer
    // Explicitly set both buyer and seller to ensure $userCustomer is neither
    $sale = Sales::factory()->create([
        'id_comprador' => $otherCustomer->id,
        'id_vendedor' => $thirdCustomer->id
    ]);

    $this->actingAs($user)
         ->get(route('sales.receipt', $sale->id))
         ->assertStatus(403);
});

test('sales terms download', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');

    $this->actingAs($user)
         ->get(route('sales.terms'))
         ->assertStatus(200);
});
