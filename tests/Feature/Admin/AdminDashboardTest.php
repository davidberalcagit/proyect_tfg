<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Admin\BrandManager;
use App\Livewire\Admin\ModelManager;
use App\Livewire\Admin\FuelManager;
use App\Livewire\Admin\ColorManager;
use App\Livewire\Admin\GearManager;
use App\Jobs\ProcessCarImageJob;
use App\Jobs\CleanupRejectedOffersJob;
use App\Jobs\AuditCarPricesJob;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create admin role
        Role::create(['name' => 'admin']);
    }

    public function test_admin_can_access_dashboard()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Resumen del Sistema');
        $response->assertSee('Acciones del Sistema');
    }

    public function test_non_admin_cannot_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403); // Or 404 depending on middleware handling
    }

    public function test_dashboard_contains_livewire_components()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertSeeLivewire(BrandManager::class)
            ->assertSeeLivewire(ModelManager::class)
            ->assertSeeLivewire(FuelManager::class)
            ->assertSeeLivewire(ColorManager::class)
            ->assertSeeLivewire(GearManager::class);
    }

    public function test_admin_can_run_artisan_commands()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Artisan::shouldReceive('call')->with('cache:clear')->once();
        Artisan::shouldReceive('output')->andReturn('Cache cleared');

        $response = $this->actingAs($admin)->post(route('admin.run-job'), [
            'job' => 'clear-cache'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_dispatch_jobs()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create a car for the process-image job
        \App\Models\Cars::factory()->create();

        Queue::fake();

        $response = $this->actingAs($admin)->post(route('admin.run-job'), [
            'job' => 'process-image'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Queue::assertPushed(ProcessCarImageJob::class);
    }

    public function test_admin_can_run_synchronous_jobs()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Queue::fake();

        $response = $this->actingAs($admin)->post(route('admin.run-job'), [
            'job' => 'cleanup-offers'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Queue::assertPushed(CleanupRejectedOffersJob::class);
    }
}
