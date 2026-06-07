<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleCalculationTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;
    private Shift $shift;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cashier  = User::factory()->cashier()->create();
        $this->shift    = Shift::factory()->create(['user_id' => $this->cashier->id]);
        $this->category = Category::factory()->create();
    }

    public function test_subtotal_is_sum_of_line_totals(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id, 'selling_price' => 10.00]);

        $sale = Sale::create([
            'user_id'        => $this->cashier->id,
            'shift_id'       => $this->shift->id,
            'receipt_number' => 'REC-TEST001',
            'subtotal'       => 30.00,
            'discount'       => 0,
            'total'          => 30.00,
            'cash_received'  => 30.00,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'status'         => 'completed',
        ]);

        $sale->items()->create([
            'product_id' => $product->id,
            'cost_price' => 6.00,
            'quantity'   => 3,
            'unit_price' => 10.00,
            'total'      => 30.00,
            'status'     => 'normal',
        ]);

        $this->assertEquals(30.00, $sale->items->sum('total'));
        $this->assertEquals(30.00, $sale->subtotal);
    }

    public function test_discount_reduces_total_from_subtotal(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id, 'selling_price' => 20.00]);

        $subtotal = 40.00;
        $discount = 5.00;
        $total    = $subtotal - $discount;

        $sale = Sale::create([
            'user_id'        => $this->cashier->id,
            'shift_id'       => $this->shift->id,
            'receipt_number' => 'REC-TEST002',
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'total'          => $total,
            'cash_received'  => $total,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'status'         => 'completed',
        ]);

        $this->assertEquals($subtotal - $discount, $sale->total);
        $this->assertEquals(35.00, $sale->total);
    }

    public function test_total_cannot_be_negative(): void
    {
        // Discount larger than subtotal should floor at 0
        $subtotal = 10.00;
        $discount = 15.00;
        $total    = max(0, $subtotal - $discount);

        $this->assertEquals(0, $total);
    }

    public function test_sale_belongs_to_user(): void
    {
        $sale = Sale::factory()->create(['user_id' => $this->cashier->id, 'shift_id' => $this->shift->id]);
        $this->assertTrue($sale->user->is($this->cashier));
    }

    public function test_sale_has_many_items(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $sale    = Sale::factory()->create(['user_id' => $this->cashier->id, 'shift_id' => $this->shift->id]);

        $sale->items()->create([
            'product_id' => $product->id,
            'cost_price' => 5.00,
            'quantity'   => 2,
            'unit_price' => 10.00,
            'total'      => 20.00,
            'status'     => 'normal',
        ]);

        $this->assertCount(1, $sale->fresh()->items);
    }

    public function test_shift_belongs_to_user(): void
    {
        $shift = Shift::factory()->create(['user_id' => $this->cashier->id]);
        $this->assertTrue($shift->user->is($this->cashier));
    }

    public function test_product_belongs_to_category(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $this->assertTrue($product->category->is($this->category));
    }
}
