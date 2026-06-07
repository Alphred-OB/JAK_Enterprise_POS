<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Simulates a complete cashier shift — no actingAs() shortcuts.
 * Every request flows through the full HTTP stack exactly as a browser would.
 */
class CashierWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_cashier_shift_lifecycle(): void
    {
        // Arrange
        $cashier  = User::factory()->cashier()->create(['password' => bcrypt('password')]);
        $category = Category::factory()->create(['name' => 'Beverages']);
        $water    = Product::factory()->create([
            'category_id'    => $category->id,
            'name'           => 'Aqua Water 500ml',
            'selling_price'  => 2.50,
            'cost_price'     => 1.20,
            'stock_quantity' => 100,
        ]);
        $juice = Product::factory()->create([
            'category_id'    => $category->id,
            'name'           => 'Minute Maid Orange',
            'selling_price'  => 4.00,
            'cost_price'     => 2.50,
            'stock_quantity' => 60,
        ]);

        // 1. Visit login page
        $this->get(route('login'))->assertOk();

        // 2. Submit login form — no actingAs() shortcut
        $this->post('/login', ['email' => $cashier->email, 'password' => 'password'])
            ->assertRedirect(route('pos.index'));

        $this->assertAuthenticatedAs($cashier);

        // 3. Land on POS
        $this->get(route('pos.index'))->assertOk();

        // 4. No open shift yet
        $this->getJson('/api/shifts/current')
            ->assertOk()
            ->assertJson(['has_open_shift' => false]);

        // 5. Open shift with GH₵ 150 float
        $this->postJson('/api/shifts/open', ['opening_cash' => 150.00])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('shifts', [
            'user_id'      => $cashier->id,
            'status'       => 'open',
            'opening_cash' => 150.00,
        ]);

        // 6. Confirm shift is now active
        $this->getJson('/api/shifts/current')
            ->assertOk()
            ->assertJson(['has_open_shift' => true]);

        // 7. First sale: 3 waters + 2 juices, cash — (3×2.50) + (2×4.00) = 15.50
        $sale1 = $this->postJson('/api/sales', [
            'items'          => [
                ['id' => $water->id, 'qty' => 3],
                ['id' => $juice->id, 'qty' => 2],
            ],
            'payment_method' => 'cash',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertEquals(15.50, $sale1->json('sale.subtotal'));
        $this->assertEquals(15.50, $sale1->json('sale.total'));

        // 8. Stock decremented
        $this->assertDatabaseHas('products', ['id' => $water->id, 'stock_quantity' => 97]);
        $this->assertDatabaseHas('products', ['id' => $juice->id,  'stock_quantity' => 58]);

        // 9. Second sale: 4 waters with GH₵ 2 discount, MoMo — 4×2.50=10 − 2 = 8.00
        $sale2 = $this->postJson('/api/sales', [
            'items'          => [['id' => $water->id, 'qty' => 4]],
            'discount'       => 2.00,
            'payment_method' => 'momo',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertEquals(10.00, $sale2->json('sale.subtotal'));
        $this->assertEquals(8.00,  $sale2->json('sale.total'));

        // 10. Shift close preview — expected cash = 150 opening + 15.50 cash = 165.50
        $preview = $this->postJson('/api/shifts/close', ['preview' => true])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(165.50, $preview->json('summary.expected_cash'));
        $this->assertEquals(8.00,   $preview->json('summary.momo'));

        // 11. Close shift with actual counted cash
        $this->postJson('/api/shifts/close', ['closing_cash' => 164.00])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('shifts', ['user_id' => $cashier->id, 'status' => 'closed']);

        // 12. Shift is gone after close
        $this->getJson('/api/shifts/current')
            ->assertOk()
            ->assertJson(['has_open_shift' => false]);

        // 13. Logout — full session lifecycle proven in AuthTest
        $this->post(route('logout'))->assertRedirect('/');
    }

    public function test_cashier_cannot_sell_without_an_open_shift(): void
    {
        $cashier  = User::factory()->cashier()->create(['password' => bcrypt('password')]);
        $category = Category::factory()->create();
        $product  = Product::factory()->create(['category_id' => $category->id]);

        $this->post('/login', ['email' => $cashier->email, 'password' => 'password'])
            ->assertRedirect(route('pos.index'));

        $this->postJson('/api/sales', [
            'items'          => [['id' => $product->id, 'qty' => 1]],
            'payment_method' => 'cash',
        ])->assertStatus(403)->assertJson(['success' => false]);
    }

    public function test_deactivated_cashier_cannot_login(): void
    {
        $cashier = User::factory()->cashier()->inactive()->create([
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', ['email' => $cashier->email, 'password' => 'password'])
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_full_manager_workflow(): void
    {
        $manager  = User::factory()->manager()->create(['password' => bcrypt('password')]);

        // 1. Login as manager
        $this->post('/login', ['email' => $manager->email, 'password' => 'password'])
            ->assertRedirect(route('manager.dashboard'));

        $this->assertAuthenticatedAs($manager);

        // 2. Manager dashboard loads
        $this->get(route('manager.dashboard'))->assertOk();

        // 3. Manager can access products page
        $this->get(route('manager.products.index'))->assertOk();

        // 4. Logout
        $this->post(route('logout'))->assertRedirect('/');
    }
}
