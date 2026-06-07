<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))->assertStatus(200);
    }

    public function test_cashier_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->cashier()->create(['password' => bcrypt('secret123')]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->cashier()->create(['password' => bcrypt('correct')]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong',
        ])->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_deactivated_account_cannot_login(): void
    {
        $user = User::factory()->cashier()->inactive()->create(['password' => bcrypt('password')]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_manager_is_redirected_to_manager_dashboard_after_login(): void
    {
        $manager = User::factory()->manager()->create(['password' => bcrypt('password')]);

        $this->post('/login', [
            'email'    => $manager->email,
            'password' => 'password',
        ])->assertRedirect(route('manager.dashboard'));
    }

    public function test_cashier_is_redirected_to_pos_after_login(): void
    {
        $cashier = User::factory()->cashier()->create(['password' => bcrypt('password')]);

        $this->post('/login', [
            'email'    => $cashier->email,
            'password' => 'password',
        ])->assertRedirect(route('pos.index'));
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->cashier()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_from_pos(): void
    {
        $this->get(route('pos.index'))->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_is_redirected_from_manager_dashboard(): void
    {
        $this->get(route('manager.dashboard'))->assertRedirect(route('login'));
    }
}
