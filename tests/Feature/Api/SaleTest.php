<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;
    private Shift $shift;
    private Product $productA;
    private Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cashier = User::factory()->cashier()->create();
        $this->shift = Shift::factory()->create(['user_id' => $this->cashier->id]);

        $category = Category::factory()->create();
        $this->productA = Product::factory()->create([
            'category_id'    => $category->id,
            'selling_price'  => 10.00,
            'cost_price'     => 6.00,
            'stock_quantity' => 50,
        ]);
        $this->productB = Product::factory()->create([
            'category_id'    => $category->id,
            'selling_price'  => 25.00,
            'cost_price'     => 15.00,
            'stock_quantity' => 20,
        ]);
    }

    public function test_cashier_can_complete_a_sale(): void
    {
        $payload = [
            'items'          => [['id' => $this->productA->id, 'qty' => 2]],
            'payment_method' => 'cash',
        ];

        $this->actingAs($this->cashier)
            ->postJson('/api/sales', $payload)
            ->assertOk()
            ->assertJson([
                'success' => true,
                'sale'    => ['subtotal' => 20.00, 'total' => 20.00, 'discount' => 0],
            ]);

        $this->assertDatabaseHas('sales', ['subtotal' => 20.00, 'total' => 20.00, 'shift_id' => $this->shift->id]);
    }

    public function test_sale_with_discount_calculates_total_correctly(): void
    {
        $payload = [
            'items'          => [['id' => $this->productA->id, 'qty' => 3]],
            'discount'       => 5.00,
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($this->cashier)
            ->postJson('/api/sales', $payload)
            ->assertOk();

        $this->assertEquals(30.00, $response->json('sale.subtotal'));
        $this->assertEquals(5.00,  $response->json('sale.discount'));
        $this->assertEquals(25.00, $response->json('sale.total'));
    }

    public function test_sale_with_multiple_items(): void
    {
        $payload = [
            'items'          => [
                ['id' => $this->productA->id, 'qty' => 2],
                ['id' => $this->productB->id, 'qty' => 1],
            ],
            'payment_method' => 'momo',
        ];

        $response = $this->actingAs($this->cashier)
            ->postJson('/api/sales', $payload)
            ->assertOk();

        $this->assertEquals(45.00, $response->json('sale.subtotal'));
        $this->assertEquals(45.00, $response->json('sale.total'));
    }

    public function test_sale_decrements_product_stock(): void
    {
        $initialStock = $this->productA->stock_quantity;

        $this->actingAs($this->cashier)
            ->postJson('/api/sales', [
                'items'          => [['id' => $this->productA->id, 'qty' => 3]],
                'payment_method' => 'cash',
            ])->assertOk();

        $this->assertDatabaseHas('products', [
            'id'             => $this->productA->id,
            'stock_quantity' => $initialStock - 3,
        ]);
    }

    public function test_debt_sale_increments_customer_debt(): void
    {
        $customer = Customer::factory()->create(['total_debt' => 0]);

        $this->actingAs($this->cashier)
            ->postJson('/api/sales', [
                'items'          => [['id' => $this->productA->id, 'qty' => 2]],
                'payment_method' => 'debt',
                'customer_id'    => $customer->id,
            ])->assertOk();

        $this->assertDatabaseHas('customers', [
            'id'         => $customer->id,
            'total_debt' => 20.00,
        ]);
    }

    public function test_sale_fails_validation_with_no_items(): void
    {
        $this->actingAs($this->cashier)
            ->postJson('/api/sales', ['payment_method' => 'cash', 'items' => []])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
    }

    public function test_sale_fails_validation_with_invalid_product(): void
    {
        $this->actingAs($this->cashier)
            ->postJson('/api/sales', [
                'items'          => [['id' => 'non-existent-uuid', 'qty' => 1]],
                'payment_method' => 'cash',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.id']);
    }

    public function test_sale_fails_validation_with_missing_payment_method(): void
    {
        $this->actingAs($this->cashier)
            ->postJson('/api/sales', [
                'items' => [['id' => $this->productA->id, 'qty' => 1]],
            ])->assertUnprocessable()
            ->assertJsonValidationErrors(['payment_method']);
    }

    public function test_sale_blocked_without_open_shift(): void
    {
        $this->shift->update(['status' => 'closed', 'closed_at' => now()]);

        $this->actingAs($this->cashier)
            ->postJson('/api/sales', [
                'items'          => [['id' => $this->productA->id, 'qty' => 1]],
                'payment_method' => 'cash',
            ])->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_sale_generates_unique_receipt_number(): void
    {
        $makeItem = fn() => ['id' => $this->productA->id, 'qty' => 1];

        $res1 = $this->actingAs($this->cashier)->postJson('/api/sales', ['items' => [$makeItem()], 'payment_method' => 'cash']);
        $res2 = $this->actingAs($this->cashier)->postJson('/api/sales', ['items' => [$makeItem()], 'payment_method' => 'cash']);

        $this->assertNotEquals(
            $res1->json('receipt_number'),
            $res2->json('receipt_number')
        );
    }

    public function test_unauthenticated_request_to_sales_returns_401(): void
    {
        $this->postJson('/api/sales', [])->assertUnauthorized();
        $this->getJson('/api/sales')->assertUnauthorized();
    }
}
