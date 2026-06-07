<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class LoginPage extends Page
{
    public function url(): string
    {
        return '/login';
    }

    public function assert(Browser $browser): void
    {
        // Wait for Alpine's 50ms booted timeout to show the form
        $browser->assertPathIs($this->url())
                ->waitFor('input[name="email"]', 5);
    }

    public function elements(): array
    {
        return [
            '@email'    => 'input[name="email"]',
            '@password' => 'input[name="password"]',
            '@submit'   => 'button[type="submit"]',
        ];
    }

    public function submitLogin(Browser $browser, string $email, string $password): void
    {
        // waitForReload() snapshots the current DOM, runs the callback (which
        // submits the form), then polls until the page changes — works for both
        // successful redirects and /login reloads with errors.
        $browser->waitFor('@email', 5)
                ->clear('@email')
                ->type('@email', $email)
                ->clear('@password')
                ->type('@password', $password)
                ->waitFor('@submit', 5)
                ->waitForReload(function (Browser $browser) {
                    $browser->click('@submit');
                }, 15);
    }
}
