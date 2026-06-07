<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;

class PosCheckoutTest extends DuskTestCase
{
    private array $createdUsers      = [];
    private array $createdProducts   = [];
    private array $createdCategories = [];

    protected function tearDown(): void
    {
        // Delete in FK-safe order:
        // sales (cascade → sale_items/sale_returns) → shifts → user → products → categories
        foreach ($this->createdUsers as $u) {
            Sale::where('user_id', $u->id)->delete();
            Shift::where('user_id', $u->id)->delete();
            $u->forceDelete();
        }
        foreach ($this->createdProducts as $p) {
            $p->delete();
        }
        foreach ($this->createdCategories as $c) {
            $c->delete();
        }
        parent::tearDown();
    }

    private function freshBrowse(callable $callback): void
    {
        $this->browse(function (Browser $browser) use ($callback) {
            $browser->driver->get(config('app.url') . '/login');
            $browser->driver->manage()->deleteAllCookies();
            $callback($browser);
        });
    }

    private function makeCashier(): User
    {
        $u = User::factory()->cashier()->create(['password' => Hash::make('password')]);
        $this->createdUsers[] = $u;
        return $u;
    }

    private function makeCategory(string $name = 'Test Category'): Category
    {
        $c = Category::factory()->create(['name' => $name]);
        $this->createdCategories[] = $c;
        return $c;
    }

    private function makeProduct(Category $category, array $overrides = []): Product
    {
        $p = Product::factory()->create(array_merge([
            'category_id'    => $category->id,
            'selling_price'  => 5.00,
            'stock_quantity' => 100,
            'is_active'      => true,
        ], $overrides));
        $this->createdProducts[] = $p;
        return $p;
    }

    private function loginAndOpenShift(Browser $browser, User $cashier, float $opening = 100.00): void
    {
        $browser->visit(new LoginPage)
                ->submitLogin($cashier->email, 'password')
                ->assertPathIs('/pos')
                ->waitFor('[dusk="opening-cash-input"]', 10);

        // Ensure CSRF is ready before API calls — init()'s sanctum/csrf-cookie
        // can fall into its catch block under server load, showing the modal
        // WITHOUT the XSRF-TOKEN cookie set.
        $browser->script("axios.get('/sanctum/csrf-cookie').catch(function(){})");
        $browser->pause(800);

        // script() returns the JS result, not $this — must break the chain
        $browser->script("
            var el = document.querySelector('[dusk=\"opening-cash-input\"]');
            el.value = '{$opening}';
            el.dispatchEvent(new Event('input', { bubbles: true }));
        ");

        $browser->click('[dusk="start-selling-btn"]')
                ->waitFor('[dusk="close-shift-btn"]', 15);
    }

    public function test_pos_page_loads_with_products(): void
    {
        $category = $this->makeCategory('Beverages');
        $this->makeProduct($category, ['name' => 'Aqua Water 500ml']);
        $cashier = $this->makeCashier();

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $browser->visit(new LoginPage)
                    ->submitLogin($cashier->email, 'password')
                    ->assertPathIs('/pos')
                    ->waitForText('Aqua Water 500ml', 10)
                    ->assertSee('Aqua Water 500ml');
        });
    }

    public function test_open_shift_modal_appears_when_no_shift(): void
    {
        $cashier = $this->makeCashier();

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $browser->visit(new LoginPage)
                    ->submitLogin($cashier->email, 'password')
                    ->assertPathIs('/pos')
                    ->waitFor('[dusk="opening-cash-input"]', 10)
                    ->assertVisible('[dusk="opening-cash-input"]');
        });
    }

    public function test_cashier_can_open_shift_via_modal(): void
    {
        $cashier = $this->makeCashier();

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $this->loginAndOpenShift($browser, $cashier, 200.00);
            $browser->assertVisible('[dusk="close-shift-btn"]');
        });
    }

    public function test_product_can_be_added_to_cart(): void
    {
        $category = $this->makeCategory('Beverages');
        $product  = $this->makeProduct($category, ['name' => 'Aqua Water 500ml', 'selling_price' => 2.50]);
        $cashier  = $this->makeCashier();

        $this->freshBrowse(function (Browser $browser) use ($cashier, $product) {
            $this->loginAndOpenShift($browser, $cashier, 100.00);

            $browser->waitFor('[dusk="product-card-' . $product->id . '"]', 10)
                    ->click('[dusk="product-card-' . $product->id . '"]')
                    ->pause(500)
                    ->assertSee('Aqua Water 500ml');
        });
    }

    public function test_product_search_filters_results(): void
    {
        $category = $this->makeCategory('Mixed');
        $this->makeProduct($category, ['name' => 'Aqua Water 500ml',       'selling_price' => 2.50]);
        $this->makeProduct($category, ['name' => 'Pampers Diapers Size 4', 'selling_price' => 65.00]);
        $cashier = $this->makeCashier();

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $browser->visit(new LoginPage)
                    ->submitLogin($cashier->email, 'password')
                    ->assertPathIs('/pos')
                    ->waitForText('Aqua Water 500ml', 10)
                    ->type('input[x-ref="searchInput"]', 'Pampers')
                    ->pause(500)
                    ->assertSee('Pampers Diapers Size 4')
                    ->assertDontSee('Aqua Water 500ml');
        });
    }

    public function test_full_checkout_flow_shows_receipt_modal(): void
    {
        $category = $this->makeCategory('Beverages');
        $product  = $this->makeProduct($category, ['name' => 'Aqua Water 500ml', 'selling_price' => 2.50]);
        $cashier  = $this->makeCashier();

        $this->freshBrowse(function (Browser $browser) use ($cashier, $product) {
            $this->loginAndOpenShift($browser, $cashier, 100.00);

            $browser->waitFor('[dusk="product-card-' . $product->id . '"]', 10)
                    ->click('[dusk="product-card-' . $product->id . '"]')
                    ->pause(500)
                    ->click('[dusk="checkout-btn"]')
                    ->waitFor('[dusk="success-overlay"]', 15)
                    ->assertSee('Sale Complete!');
        });
    }
}
