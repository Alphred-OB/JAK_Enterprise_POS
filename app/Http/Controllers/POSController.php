<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Cache;

class POSController extends Controller
{
    public function index()
    {
        // Select only the columns the POS UI needs — avoids loading description, timestamps, etc.
        $products = Product::with('category:id,name')
            ->where('is_active', true)
            ->select(['id', 'name', 'selling_price', 'stock_quantity', 'image_path', 'category_id', 'low_stock_threshold'])
            ->get();

        try {
            $categories = Cache::remember('categories:all', 3600, fn() => Category::select(['id', 'name'])->get());
            // Validate cached data is a proper Collection of model objects, not stale/corrupt data
            if (!($categories instanceof \Illuminate\Support\Collection) ||
                ($categories->isNotEmpty() && !\is_object($categories->first()))) {
                throw new \Exception('Stale cache');
            }
        } catch (\Exception $e) {
            Cache::forget('categories:all');
            $categories = Category::select(['id', 'name'])->get();
        }

        // Limit initial customer list; the manager can use search for the rest
        $customers = Customer::select(['id', 'name', 'phone', 'total_debt'])
            ->orderBy('name')
            ->limit(200)
            ->get();

        return view('pos.index', compact('products', 'categories', 'customers'));
    }

    public function receipt(Sale $sale)
    {
        $user = auth()->user();
        if ($sale->user_id !== $user->id && !in_array($user->role, ['manager', 'admin'])) {
            abort(403);
        }

        $sale->load(['items.product', 'user']);
        return view('pos.receipt', compact('sale'));
    }
}
