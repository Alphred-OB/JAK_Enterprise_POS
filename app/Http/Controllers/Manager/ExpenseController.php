<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('user')->latest()->paginate(15);
        return view('manager.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $categories = ['Rent', 'Electricity', 'Water', 'Staff Lunch', 'Transport', 'Stock Purchase', 'Damaged Goods', 'Other'];
        return view('manager.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
        ]);

        Expense::create([
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('manager.expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('manager.expenses.index')->with('success', 'Expense record deleted.');
    }
}
