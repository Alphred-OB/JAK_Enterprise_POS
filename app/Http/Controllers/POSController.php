<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::all();
        $customers = Customer::orderBy('name')->get();
        
        return view('pos.index', compact('products', 'categories', 'customers'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'user']);
        return view('pos.receipt', compact('sale'));
    }
}
