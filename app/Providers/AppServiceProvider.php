<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->share('settings', \App\Models\Setting::first() ?? new \App\Models\Setting());

        // Global sidebar variables for the manager layout
        view()->composer('layouts.manager', function ($view) {
            $lowStockCount = \App\Models\Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
            $view->with('lowStockCount', $lowStockCount);
        });
    }
}
