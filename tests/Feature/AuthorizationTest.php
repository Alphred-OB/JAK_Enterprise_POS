<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_cannot_access_manager_dashboard(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->get(route('manager.dashboard'))
            ->assertRedirect(route('pos.index'));
    }

    public function test_manager_can_access_manager_dashboard(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('manager.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_access_manager_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('manager.dashboard'))
            ->assertOk();
    }

    public function test_cashier_cannot_access_admin_only_routes(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('pos.index'));
    }

    public function test_manager_cannot_access_admin_only_routes(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_admin_only_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_deactivated_user_is_logged_out_on_request(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user);

        // Deactivate mid-session (bypass fillable guard — this is a deliberate test setup)
        $user->forceFill(['is_active' => false])->save();

        $this->get(route('manager.dashboard'))
            ->assertRedirect('login');
    }
}
