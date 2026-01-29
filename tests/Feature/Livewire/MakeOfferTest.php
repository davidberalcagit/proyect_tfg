<?php

use App\Livewire\MakeOffer;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('can make offer', function () {
    $seller = User::factory()->create();
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

    $buyer = User::factory()->create();
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyer->id]);

    $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

    $buyer = $buyer->fresh();

    Livewire::actingAs($buyer)
        ->test(MakeOffer::class, ['car' => $car])
        ->set('cantidad', 10000)
        ->call('submitOffer')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('offers', [
        'id_vehiculo' => $car->id,
        'id_comprador' => $buyerCustomer->id,
        'cantidad' => 10000
    ]);
});

test('cannot offer on own car', function () {
    $seller = User::factory()->create();
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);
    $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

    $seller = $seller->fresh();

    Livewire::actingAs($seller)
        ->test(MakeOffer::class, ['car' => $car])
        ->call('openModal')
        ->assertSet('isModalOpen', false);
});

test('cannot make duplicate pending offer', function () {
    $seller = User::factory()->create();
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

    $buyer = User::factory()->create();
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyer->id]);

    $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

    Offer::factory()->create([
        'id_vehiculo' => $car->id,
        'id_comprador' => $buyerCustomer->id,
        'estado' => 'pending'
    ]);

    $buyer = $buyer->fresh();

    Livewire::actingAs($buyer)
        ->test(MakeOffer::class, ['car' => $car])
        ->set('cantidad', 12000)
        ->call('submitOffer');

    $this->assertDatabaseCount('offers', 1);
});
