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
            
            // Count suspicious/flagged activities from today
            $flaggedCount = \App\Models\Activity::whereDate('created_at', now()->today())
                ->whereIn('action', ['stock_adjusted', 'sale_cancelled', 'discount_applied', 'price_changed', 'issue_reported', 'inventory_conflict'])
                ->count();

            // Count returns from today
            $returnCount = \App\Models\SaleReturn::whereDate('created_at', now()->today())->count();

            // Count active shifts
            $activeShiftCount = \App\Models\Shift::whereNull('closed_at')->count();

            $view->with([
                'lowStockCount' => $lowStockCount,
                'flaggedCount' => $flaggedCount,
                'returnCount' => $returnCount,
                'activeShiftCount' => $activeShiftCount
            ]);
        });
    }
}
