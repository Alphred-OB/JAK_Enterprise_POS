<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PinVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_manager_pin_is_accepted(): void
    {
        $manager = User::factory()->manager()->create([
            'pin_code' => Hash::make('1234'),
        ]);
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/verify-pin', ['pin' => '1234', 'action' => 'apply_discount'])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_valid_admin_pin_is_accepted(): void
    {
        $admin = User::factory()->admin()->create([
            'pin_code' => Hash::make('9999'),
        ]);
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/verify-pin', ['pin' => '9999', 'action' => 'apply_discount'])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_wrong_pin_is_rejected(): void
    {
        User::factory()->manager()->create(['pin_code' => Hash::make('1234')]);
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/verify-pin', ['pin' => '0000', 'action' => 'apply_discount'])
            ->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_inactive_manager_pin_is_rejected(): void
    {
        User::factory()->manager()->inactive()->create(['pin_code' => Hash::make('5678')]);
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/verify-pin', ['pin' => '5678', 'action' => 'apply_discount'])
            ->assertStatus(403);
    }

    public function test_pin_with_no_managers_is_rejected(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/verify-pin', ['pin' => '1234', 'action' => 'apply_discount'])
            ->assertStatus(403);
    }

    public function test_pin_validation_requires_4_digit_pin(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/verify-pin', ['pin' => '12', 'action' => 'apply_discount'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['pin']);
    }

    public function test_pin_validation_requires_action(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/verify-pin', ['pin' => '1234'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['action']);
    }

    public function test_unauthenticated_pin_request_returns_401(): void
    {
        $this->postJson('/api/verify-pin', ['pin' => '1234', 'action' => 'test'])->assertUnauthorized();
    }
}
