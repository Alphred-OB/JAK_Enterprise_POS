<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('has_debt')) {
            $query->where('total_debt', '>', 0);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();
        return view('manager.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('manager.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'total_debt' => 'nullable|numeric|min:0',
        ]);

        Customer::create($request->only(['name', 'phone', 'email', 'address', 'total_debt']));

        return redirect()->route('manager.customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('manager.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'total_debt' => 'nullable|numeric|min:0',
        ]);

        $customer->update($request->only(['name', 'phone', 'email', 'address', 'total_debt']));

        return redirect()->route('manager.customers.index')->with('success', 'Customer profile updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('manager.customers.index')->with('success', 'Customer record deleted.');
    }

    public function repayment(Request $request, Customer $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $customer->total_debt,
        ]);

        $customer->decrement('total_debt', $request->amount);

        \App\Models\Activity::log('debt_repayment', "GH₵ {$request->amount} debt repayment received from {$customer->name}", [
            'customer_id' => $customer->id,
            'amount' => $request->amount
        ]);

        return redirect()->back()->with('success', 'Repayment of GH₵ ' . number_format($request->amount, 2) . ' recorded for ' . $customer->name);
    }
}
