<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ToggleFavorite;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('toggle favorite adds and removes favorite', function () {
    $user = User::factory()->create();

    $brand = Brands::factory()->create();
    $model = CarModels::factory()->create(['id_marca' => $brand->id]);
    $fuel = Fuels::factory()->create();
    $gear = Gears::factory()->create();
    $color = Color::factory()->create();
    $listingType = ListingType::factory()->create();

    $car = Cars::factory()->create([
        'id_marca' => $brand->id,
        'id_modelo' => $model->id,
        'id_combustible' => $fuel->id,
        'id_marcha' => $gear->id,
        'id_color' => $color->id,
        'id_listing_type' => $listingType->id,
    ]);

    // Ensure clean state
    $user->favorites()->detach();

    // Test Toggle ON
    Livewire::actingAs($user)
        ->test(ToggleFavorite::class, ['car' => $car])
        ->assertSet('isFavorite', false)
        ->call('toggle')
        ->assertSet('isFavorite', true);

    $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'car_id' => $car->id]);

    // Test Toggle OFF (New component instance to simulate page reload)
    // We need to refresh user to see the new relation
    $user = $user->fresh();

    Livewire::actingAs($user)
        ->test(ToggleFavorite::class, ['car' => $car])
        ->assertSet('isFavorite', true)
        ->call('toggle')
        ->assertSet('isFavorite', false);

    $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'car_id' => $car->id]);
});
