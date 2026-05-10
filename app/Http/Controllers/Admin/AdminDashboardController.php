<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sale;
use App\Models\Expense;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalRevenue = Sale::sum('total');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;
        
        $managers = User::where('role', 'manager')->count();
        $cashiers = User::where('role', 'cashier')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'managers',
            'cashiers'
        ));
    }
}
