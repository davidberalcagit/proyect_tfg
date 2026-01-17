<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DashboardRedirectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_route_redirects_to_cars_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/cars');
    }

    public function test_authenticated_user_is_redirected_to_cars_after_login()
    {
        // Debug config
        // dump("Fortify Home Config: " . config('fortify.home'));

        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/cars');
    }
}
