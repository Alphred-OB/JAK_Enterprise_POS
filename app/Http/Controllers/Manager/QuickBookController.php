<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuickBookController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Sale::with(['user', 'customer', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by Cashier
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Payment Method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by Customer Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('invoice_no', 'like', "%{$search}%");
        }

        $sales = $query->latest()->paginate(20)->withQueryString();
        $cashiers = User::whereIn('role', ['manager', 'admin', 'cashier'])->get();

        return view('manager.quick-book.index', [
            'sales' => $sales,
            'cashiers' => $cashiers,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalAmount' => $query->sum('total'),
            'totalTransactions' => $query->count()
        ]);
    }
}
