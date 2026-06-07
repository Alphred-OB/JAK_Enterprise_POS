<?php

namespace Tests\Feature\Api;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_shift_returns_no_shift_when_none_open(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->getJson('/api/shifts/current')
            ->assertOk()
            ->assertJson(['has_open_shift' => false, 'shift' => null]);
    }

    public function test_current_shift_returns_open_shift(): void
    {
        $cashier = User::factory()->cashier()->create();
        $shift = Shift::factory()->create(['user_id' => $cashier->id]);

        $this->actingAs($cashier)
            ->getJson('/api/shifts/current')
            ->assertOk()
            ->assertJson(['has_open_shift' => true])
            ->assertJsonPath('shift.id', $shift->id);
    }

    public function test_cashier_can_open_a_shift(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/shifts/open', ['opening_cash' => 200.00])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('shifts', [
            'user_id'      => $cashier->id,
            'status'       => 'open',
            'opening_cash' => 200.00,
        ]);
    }

    public function test_opening_shift_requires_opening_cash(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/shifts/open', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['opening_cash']);
    }

    public function test_opening_new_shift_auto_closes_existing_open_shift(): void
    {
        $cashier = User::factory()->cashier()->create();
        $old = Shift::factory()->create(['user_id' => $cashier->id, 'status' => 'open']);

        $this->actingAs($cashier)
            ->postJson('/api/shifts/open', ['opening_cash' => 100.00])
            ->assertOk();

        $this->assertDatabaseHas('shifts', [
            'id'     => $old->id,
            'status' => 'closed',
        ]);

        $this->assertEquals(1, Shift::where('user_id', $cashier->id)->where('status', 'open')->count());
    }

    public function test_cashier_can_close_an_open_shift(): void
    {
        $cashier = User::factory()->cashier()->create();
        Shift::factory()->create(['user_id' => $cashier->id, 'status' => 'open', 'opening_cash' => 100]);

        $response = $this->actingAs($cashier)
            ->postJson('/api/shifts/close', ['closing_cash' => 250.00])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(250.00, $response->json('summary.actual_cash'));

        $this->assertDatabaseHas('shifts', [
            'user_id' => $cashier->id,
            'status'  => 'closed',
        ]);
    }

    public function test_closing_shift_requires_closing_cash(): void
    {
        $cashier = User::factory()->cashier()->create();
        Shift::factory()->create(['user_id' => $cashier->id, 'status' => 'open']);

        $this->actingAs($cashier)
            ->postJson('/api/shifts/close', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['closing_cash']);
    }

    public function test_closing_shift_when_none_open_returns_404(): void
    {
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($cashier)
            ->postJson('/api/shifts/close', ['closing_cash' => 100])
            ->assertNotFound();
    }

    public function test_shift_preview_returns_summary_without_closing(): void
    {
        $cashier = User::factory()->cashier()->create();
        Shift::factory()->create(['user_id' => $cashier->id, 'status' => 'open', 'opening_cash' => 100]);

        $this->actingAs($cashier)
            ->postJson('/api/shifts/close', ['preview' => true])
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['summary' => ['expected_cash', 'momo', 'card', 'debt']]);

        $this->assertDatabaseMissing('shifts', ['user_id' => $cashier->id, 'status' => 'closed']);
    }

    public function test_unauthenticated_request_to_shifts_returns_401(): void
    {
        $this->getJson('/api/shifts/current')->assertUnauthorized();
        $this->postJson('/api/shifts/open', ['opening_cash' => 100])->assertUnauthorized();
        $this->postJson('/api/shifts/close', ['closing_cash' => 100])->assertUnauthorized();
    }
}
