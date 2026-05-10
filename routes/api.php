<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Sales
    Route::get('/sales', [SaleController::class, 'index']);
    Route::post('/sales', [SaleController::class, 'store']);

    // Shifts
    Route::get('/shifts/current', [ShiftController::class, 'current']);
    Route::post('/shifts/open', [ShiftController::class, 'open']);
    Route::post('/shifts/close', [ShiftController::class, 'close']);

    // Customers (Quick Add)
    Route::post('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'store']);

    // Security & Overrides
    Route::post('/verify-pin', [\App\Http\Controllers\Api\PinVerificationController::class, 'verify']);

    // Support
    Route::post('/support-reports', [SupportController::class, 'store']);

    // Auth/Security
    Route::post('/verify-pin', [AuthController::class, 'verifyPin']);
});
