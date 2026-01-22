<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Individuals;
use Database\Seeders\EntityTypesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterIndividualTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_individual_user_can_register_successfully()
    {
        $this->seed(RolesAndPermissionsSeeder::class); // Seed roles first
        $this->seed(EntityTypesSeeder::class);

        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'type' => 'individual',

            'telefono' => '600123123',

            'id_entidad'=>'1',

            'dni' => '12345678Z',
            'fecha_nacimiento' => '1995-01-01',

            'terms' => 'on',
        ]);
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('customers', [
            'nombre_contacto' => 'John Doe',
            'telefono' => '600123123',
        ]);

        $this->assertDatabaseHas('individuals', [
            'dni' => '12345678Z',
            'fecha_nacimiento' => '1995-01-01',
        ]);

        $user = User::where('email', 'john@example.com')->first();

        $this->assertNotNull($user->customer);
        $this->assertNotNull($user->customer->individual);
    }
}
