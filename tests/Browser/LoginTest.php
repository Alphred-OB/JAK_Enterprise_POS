<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /** @var User[] */
    private array $createdUsers = [];

    protected function tearDown(): void
    {
        // Clean up any users created during this test
        foreach ($this->createdUsers as $user) {
            $user->forceDelete();
        }
        parent::tearDown();
    }

    private function makeUser(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'password' => Hash::make('password'),
        ], $attributes));
        $this->createdUsers[] = $user;
        return $user;
    }

    /** Navigate to the app domain, wipe all cookies, then run the callback. */
    private function freshBrowse(callable $callback): void
    {
        $this->browse(function (Browser $browser) use ($callback) {
            // Navigate to the app domain before deleting cookies so that
            // deleteAllCookies() targets 127.0.0.1, not about:blank's null-origin.
            $browser->driver->get(config('app.url') . '/login');
            $browser->driver->manage()->deleteAllCookies();
            $callback($browser);
        });
    }

    public function test_login_page_renders(): void
    {
        $this->freshBrowse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                    ->assertVisible('@email')
                    ->assertVisible('@password')
                    ->assertVisible('@submit');
        });
    }

    public function test_cashier_can_login_and_reach_pos(): void
    {
        $cashier = $this->makeUser(['role' => 'cashier', 'is_active' => true]);

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $browser->visit(new LoginPage)
                    ->submitLogin($cashier->email, 'password')
                    ->assertPathIs('/pos');
        });
    }

    public function test_wrong_password_shows_error(): void
    {
        $cashier = $this->makeUser(['role' => 'cashier', 'is_active' => true]);

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $browser->visit(new LoginPage)
                    ->submitLogin($cashier->email, 'wrongpassword')
                    ->assertPathIs('/login')
                    ->assertSee('credentials do not match');
        });
    }

    public function test_deactivated_account_shows_error(): void
    {
        $cashier = $this->makeUser(['role' => 'cashier', 'is_active' => false]);

        $this->freshBrowse(function (Browser $browser) use ($cashier) {
            $browser->visit(new LoginPage)
                    ->submitLogin($cashier->email, 'password')
                    ->assertPathIs('/login')
                    ->assertSee('deactivated');
        });
    }

    public function test_manager_is_redirected_to_manager_dashboard(): void
    {
        $manager = $this->makeUser(['role' => 'manager', 'is_active' => true]);

        $this->freshBrowse(function (Browser $browser) use ($manager) {
            $browser->visit(new LoginPage)
                    ->submitLogin($manager->email, 'password')
                    ->assertPathIs('/manager/dashboard');
        });
    }
}
