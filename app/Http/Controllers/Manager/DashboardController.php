<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Shift;
use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
        
        // 1. Core Financials
        $todaySales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        
        // Profit calculation: (Unit Price - Cost Price) * Quantity
        $todayProfit = SaleItem::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('SUM((unit_price - cost_price) * quantity) as profit'))
            ->first()->profit ?? 0;
            
        $todayExpenses = Expense::whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->sum('amount');
        $netProfit = $todayProfit - $todayExpenses;
 
        // 2. Payment Method Breakdown
        $paymentBreakdown = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();
 
        // 3. Operational Metrics
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        
        // Inventory Valuation (Total Cost of all stock on hand)
        $inventoryValue = Product::select(DB::raw('SUM(cost_price * stock_quantity) as total_value'))
            ->first()->total_value ?? 0;
 
        // 4. Best Selling Products (Period)
        $topProducts = SaleItem::whereBetween('created_at', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();
 
        // 5. Staff Performance Leaderboard
        $staffPerformance = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select('user_id', DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as transaction_count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total_sales', 'desc')
            ->get();
 
        // 6. Active Shifts with Details
        $activeShifts = Shift::where('status', 'open')
            ->with('user')
            ->get();
        
        // 7. Suspicious Activities (Audit)
        $suspiciousActivities = Activity::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('action', ['stock_adjusted', 'sale_cancelled', 'discount_applied'])
            ->latest()
            ->take(5)
            ->get();
 
        // 7. Sales History Chart Data (14 days from end date)
        $chartData = Sale::whereBetween('created_at', [$startDate->copy()->subDays(14), $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('manager.dashboard', [
            'todaySales' => $todaySales,
            'todayProfit' => $todayProfit,
            'todayExpenses' => $todayExpenses,
            'netProfit' => $netProfit,
            'paymentBreakdown' => $paymentBreakdown,
            'lowStockCount' => $lowStockCount,
            'inventoryValue' => $inventoryValue,
            'topProducts' => $topProducts,
            'staffPerformance' => $staffPerformance,
            'activeShifts' => $activeShifts,
            'suspiciousActivities' => $suspiciousActivities,
            'chartData' => $chartData
        ]);
    }

    public function report(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
        
        $todaySales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $todayProfit = SaleItem::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('SUM((unit_price - cost_price) * quantity) as profit'))
            ->first()->profit ?? 0;
        $todayExpenses = Expense::whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->sum('amount');
        
        $topProducts = SaleItem::whereBetween('created_at', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(10)
            ->get();

        $staffPerformance = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select('user_id', DB::raw('SUM(total) as total_sales'))
            ->with('user')
            ->groupBy('user_id')
            ->get();

        return view('manager.reports.summary', compact('startDate', 'endDate', 'todaySales', 'todayProfit', 'todayExpenses', 'topProducts', 'staffPerformance'));
    }
}
