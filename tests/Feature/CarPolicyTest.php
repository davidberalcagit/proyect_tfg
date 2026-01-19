<?php

namespace Tests\Feature;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_edit_approved_car()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $car = Cars::factory()->create([
            'id_estado' => 1 // Aprobado
        ]);

        // Acceder a la vista de edición
        $response = $this->actingAs($admin)->get(route('cars.edit', $car));
        $response->assertStatus(200);

        // Actualizar
        $response = $this->actingAs($admin)->put(route('cars.update', $car), [
            'precio' => 99999,
            // Campos requeridos mínimos para pasar validación
            'id_marca' => $car->id_marca,
            'id_modelo' => $car->id_modelo,
            'id_marcha' => $car->id_marcha,
            'id_combustible' => $car->id_combustible,
            'id_color' => $car->id_color,
            'matricula' => $car->matricula,
            'anyo_matri' => $car->anyo_matri,
            'km' => $car->km,
            'descripcion' => 'Admin edit',
        ]);

        $response->assertRedirect(route('cars.index'));
        $this->assertDatabaseHas('cars', ['id' => $car->id, 'precio' => 99999]);
    }

    public function test_owner_can_edit_pending_car()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $customer->id,
            'id_estado' => 4 // Pendiente
        ]);

        $response = $this->actingAs($user)->get(route('cars.edit', $car));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->put(route('cars.update', $car), [
            'precio' => 5000,
            'id_marca' => $car->id_marca,
            'id_modelo' => $car->id_modelo,
            'id_marcha' => $car->id_marcha,
            'id_combustible' => $car->id_combustible,
            'id_color' => $car->id_color,
            'matricula' => $car->matricula,
            'anyo_matri' => $car->anyo_matri,
            'km' => $car->km,
            'descripcion' => 'Owner edit',
        ]);

        $response->assertRedirect(route('cars.index'));
    }

    public function test_owner_cannot_edit_approved_car()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $customer->id,
            'id_estado' => 1 // Aprobado
        ]);

        $response = $this->actingAs($user)->get(route('cars.edit', $car));
        $response->assertStatus(403); // Forbidden por Policy

        $response = $this->actingAs($user)->put(route('cars.update', $car), [
            'precio' => 5000
        ]);
        $response->assertStatus(403);
    }

    public function test_user_cannot_edit_others_car()
    {
        $owner = User::factory()->create();
        $owner->assignRole('individual');
        $ownerCustomer = Customers::factory()->create(['id_usuario' => $owner->id]);

        $otherUser = User::factory()->create();
        $otherUser->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $otherUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $ownerCustomer->id,
            'id_estado' => 4 // Pendiente (incluso si es pendiente, otro no puede tocarlo)
        ]);

        $response = $this->actingAs($otherUser)->get(route('cars.edit', $car));
        $response->assertStatus(403);

        $response = $this->actingAs($otherUser)->put(route('cars.update', $car), [
            'precio' => 5000
        ]);
        $response->assertStatus(403);
    }
}
