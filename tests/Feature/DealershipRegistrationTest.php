<?php

namespace Tests\Feature;

use App\Models\Dealerships;
use App\Models\EntityType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class DealershipRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        EntityType::create(['id' => 1, 'nombre' => 'Particular']);
        EntityType::create(['id' => 2, 'nombre' => 'Concesionario']);
    }

    public function test_dealership_users_can_join_existing_company_by_nif()
    {
        $this->withoutExceptionHandling(); // <--- Added this to see the error

        // 1. Registrar el primer usuario (Jefe)
        $this->post('/register', [
            'name' => 'Jefe Concesionario',
            'email' => 'jefe@toyota.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
            'type' => 'dealership',
            'telefono' => '600111222',
            'id_entidad' => 2,
            'nombre_empresa' => 'Toyota Madrid',
            'nif' => 'B12345678',
            'direccion' => 'Calle Principal 1',
        ]);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('dealerships', [
            'nombre_empresa' => 'Toyota Madrid',
            'nif' => 'B12345678',
        ]);

        $dealership = Dealerships::where('nif', 'B12345678')->first();

        $this->post('/logout');

        // 2. Registrar el segundo usuario (Empleado)
        $this->post('/register', [
            'name' => 'Empleado Concesionario',
            'email' => 'empleado@toyota.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
            'type' => 'dealership',
            'telefono' => '600333444',
            'id_entidad' => 2,
            'nombre_empresa' => 'Toyota Madrid',
            'nif' => 'B12345678',
            'direccion' => 'Calle Principal 1',
        ]);

        $this->assertAuthenticated();

        $this->assertEquals(1, Dealerships::count());

        $empleado = User::where('email', 'empleado@toyota.com')->first();
        $this->assertEquals($dealership->id, $empleado->customer->dealership_id);
    }
}
