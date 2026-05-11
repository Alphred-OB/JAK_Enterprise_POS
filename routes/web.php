<?php

use App\Http\Controllers\POSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use Illuminate\Support\Facades\Route;

// Redirect root based on role
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'manager' || auth()->user()->role === 'admin') {
            return redirect()->route('manager.dashboard');
        }
        if (auth()->user()->role === 'inventory_officer') {
            return redirect()->route('manager.stock.audit');
        }
    }
    return redirect()->route('pos.index');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected POS Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('/pos/receipt/{sale}', [POSController::class, 'receipt'])->name('pos.receipt');

    // Inventory & Stock Routes (Accessible by Manager, Admin, and Inventory Officer)
    Route::middleware(['role:manager,admin,inventory_officer'])->prefix('manager')->group(function () {
        Route::resource('products', \App\Http\Controllers\Manager\ProductController::class)->names([
            'index' => 'manager.products.index',
            'create' => 'manager.products.create',
            'store' => 'manager.products.store',
            'edit' => 'manager.products.edit',
            'update' => 'manager.products.update',
            'destroy' => 'manager.products.destroy',
        ]);

        Route::resource('suppliers', \App\Http\Controllers\Manager\SupplierController::class)->names([
            'index' => 'manager.suppliers.index',
            'create' => 'manager.suppliers.create',
            'store' => 'manager.suppliers.store',
            'edit' => 'manager.suppliers.edit',
            'update' => 'manager.suppliers.update',
            'destroy' => 'manager.suppliers.destroy',
        ]);

        Route::resource('purchases', \App\Http\Controllers\Manager\PurchaseController::class)->names([
            'index' => 'manager.purchases.index',
            'create' => 'manager.purchases.create',
            'store' => 'manager.purchases.store',
            'show' => 'manager.purchases.show',
        ])->only(['index', 'create', 'store', 'show']);

        // Stock Audit & Stock In
        Route::get('stock/audit', [\App\Http\Controllers\Manager\StockController::class, 'audit'])->name('manager.stock.audit');
        Route::post('stock/audit', [\App\Http\Controllers\Manager\StockController::class, 'storeAudit'])->name('manager.stock.storeAudit');
        Route::get('stock/in', [\App\Http\Controllers\Manager\StockController::class, 'stockIn'])->name('manager.stock.in');
        Route::post('stock/in', [\App\Http\Controllers\Manager\StockController::class, 'storeStockIn'])->name('manager.stock.storeStockIn');

        // Report Issue
        Route::post('report-issue', [\App\Http\Controllers\Manager\ActivityController::class, 'reportIssue'])->name('manager.issues.report');
    });

    // Manager Routes (Restricted Financials)
    Route::middleware(['role:manager,admin'])->prefix('manager')->group(function () {
        Route::get('/dashboard', [ManagerDashboard::class, 'index'])->name('manager.dashboard');

        // Returns & Refunds
        Route::get('returns', [\App\Http\Controllers\Manager\ReturnController::class, 'index'])->name('manager.returns.index');
        Route::get('returns/create', [\App\Http\Controllers\Manager\ReturnController::class, 'create'])->name('manager.returns.create');
        Route::post('returns', [\App\Http\Controllers\Manager\ReturnController::class, 'store'])->name('manager.returns.store');

        // Expense Management
        Route::resource('expenses', \App\Http\Controllers\Manager\ExpenseController::class)->names([
            'index' => 'manager.expenses.index',
            'create' => 'manager.expenses.create',
            'store' => 'manager.expenses.store',
            'destroy' => 'manager.expenses.destroy',
        ]);

        // Customer Management
        Route::resource('customers', \App\Http\Controllers\Manager\CustomerController::class)->names([
            'index' => 'manager.customers.index',
            'create' => 'manager.customers.create',
            'store' => 'manager.customers.store',
            'edit' => 'manager.customers.edit',
            'update' => 'manager.customers.update',
            'destroy' => 'manager.customers.destroy',
        ]);
        Route::post('customers/{customer}/repayment', [\App\Http\Controllers\Manager\CustomerController::class, 'repayment'])->name('manager.customers.repayment');

        // Category Management
        Route::resource('categories', \App\Http\Controllers\Manager\CategoryController::class)->names([
            'index' => 'manager.categories.index',
            'store' => 'manager.categories.store',
            'update' => 'manager.categories.update',
            'destroy' => 'manager.categories.destroy',
        ])->only(['index', 'store', 'update', 'destroy']);

        // Activity & Audit Logs
        Route::get('activities', [\App\Http\Controllers\Manager\ActivityController::class, 'index'])->name('manager.activities.index');
        Route::get('activities/flagged', [\App\Http\Controllers\Manager\ActivityController::class, 'flagged'])->name('manager.activities.flagged');

        // Shift Reconciliations
        Route::get('shifts', [\App\Http\Controllers\Manager\ShiftController::class, 'index'])->name('manager.shifts.index');

        // Reports
        Route::get('report', [\App\Http\Controllers\Manager\ManagerDashboard::class, 'report'])->name('manager.report');

        // Data Exports (CSV)
        Route::prefix('export')->group(function () {
            Route::get('sales', [\App\Http\Controllers\Manager\ExportController::class, 'sales'])->name('manager.export.sales');
            Route::get('products', [\App\Http\Controllers\Manager\ExportController::class, 'products'])->name('manager.export.products');
            Route::get('expenses', [\App\Http\Controllers\Manager\ExportController::class, 'expenses'])->name('manager.export.expenses');
            Route::get('staff', [\App\Http\Controllers\Manager\ExportController::class, 'staff'])->name('manager.export.staff');
        });
    });

    // Admin Exclusive Routes (System Owners)
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Staff Management
        Route::resource('users', \App\Http\Controllers\Manager\UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
        Route::post('users/{user}/restore', [\App\Http\Controllers\Manager\UserController::class, 'restore'])->name('admin.users.restore');

        // Backup & Restore
        Route::get('backup', [\App\Http\Controllers\Manager\BackupController::class, 'index'])->name('admin.backup.index');
        Route::get('backup/download', [\App\Http\Controllers\Manager\BackupController::class, 'download'])->name('admin.backup.download');

        // Shop Settings
        Route::get('settings', [\App\Http\Controllers\Manager\SettingController::class, 'index'])->name('admin.settings.index');
        Route::put('settings', [\App\Http\Controllers\Manager\SettingController::class, 'update'])->name('admin.settings.update');
    });
});
