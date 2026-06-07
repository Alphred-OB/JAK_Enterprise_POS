<?php

namespace Tests\Browser;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;

class ShiftManagementTest extends DuskTestCase
{
    private array $createdUsers = [];

    protected function tearDown(): void
    {
        // Shifts cascade-delete when the user is deleted (onDelete('cascade')).
        // Use delete() — Shift has no SoftDeletes, so forceDelete() on a Builder
        // throws BadMethodCallException and would prevent parent::tearDown().
        foreach ($this->createdUsers as $u) {
            Shift::where('user_id', $u->id)->delete();
            $u->forceDelete();
        }
        parent::tearDown();
    }

    private function freshBrowse(callable $callback): void
    {
        $this->browse(function (Browser $browser) use ($callback) {
            // Navigate to the app domain BEFORE deleting cookies.
            // deleteAllCookies() from about:blank only targets the null-origin
            // and does NOT clear 127.0.0.1 cookies left by a previous test.
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

    /**
     * Login via the form and open a shift through the browser UI.
     * Leaves the browser on the POS page with a shift active and
     * the "Close Shift" button visible.
     */
    private function loginAndOpenShift(Browser $browser, User $cashier, float $opening = 150.00): void
    {
        $browser->visit(new LoginPage)
                ->submitLogin($cashier->email, 'password')
                ->assertPathIs('/pos')
                ->waitFor('[dusk="opening-cash-input"]', 10);

        // Ensure CSRF is ready before API calls — init()'s sanctum/csrf-cookie
        // call can lose its race under server load, falling into the catch block
        // which shows the modal WITHOUT setting the XSRF-TOKEN cookie.
        $browser->script("axios.get('/sanctum/csrf-cookie').catch(function(){})");
        $browser->pause(800);

        // script() returns JS result, not $this — must break chain
        $browser->script("
            var el = document.querySelector('[dusk=\"opening-cash-input\"]');
            el.value = '{$opening}';
            el.dispatchEvent(new Event('input', { bubbles: true }));
        ");

        $browser->click('[dusk="start-selling-btn"]')
                ->waitFor('[dusk="close-shift-btn"]', 15);
    }

    public function test_close_shift_modal_shows_expected_summary(): void
    {
        $cashier = $this->makeCashier();

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $this->loginAndOpenShift($browser, $cashier, 150.00);

            $browser->click('[dusk="close-shift-btn"]')
                    // Wait for the modal overlay to become visible
                    ->waitFor('[dusk="close-shift-modal"]', 10)
                    // Use the h2 text — not affected by Tailwind `uppercase` CSS
                    ->assertSee('End of Shift')
                    ->assertVisible('[dusk="finalize-shift-btn"]');
        });
    }

    public function test_cashier_can_close_shift_and_see_open_shift_prompt(): void
    {
        $cashier = $this->makeCashier();

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $this->loginAndOpenShift($browser, $cashier, 200.00);

            $browser->click('[dusk="close-shift-btn"]')
                    ->waitFor('[dusk="closing-cash-input"]', 10)
                    ->type('[dusk="closing-cash-input"]', '195')
                    ->click('[dusk="finalize-shift-btn"]')
                    // After closing, Alpine calls checkShiftStatus() → shows "Open Shift" modal
                    ->waitForText('Open Shift', 10);
        });

        $shift = Shift::where('user_id', $cashier->id)->latest('closed_at')->first();
        $this->assertNotNull($shift);
        $this->assertEquals('closed', $shift->status);
        $this->assertEquals(195, $shift->closing_cash);
    }
}
