<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = SaleReturn::with(['sale', 'product', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sale', function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%");
            })->orWhereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $returns = $query->latest()->paginate(15)->withQueryString();
        return view('manager.returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $sale = null;
        if ($request->filled('receipt')) {
            $receiptNumber = trim(str_replace('#', '', $request->receipt));
            $sale = Sale::with('items.product')->where('receipt_number', $receiptNumber)->first();
        }
        return view('manager.returns.create', compact('sale'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($request) {
            // Create Return Record
            SaleReturn::create([
                'sale_id' => $request->sale_id,
                'product_id' => $request->product_id,
                'user_id' => auth()->id(),
                'quantity' => $request->quantity,
                'refund_amount' => $request->refund_amount,
                'reason' => $request->reason,
            ]);

            // Restock Product
            $product = Product::find($request->product_id);
            $product->increment('stock_quantity', $request->quantity);

            // Log activity
            DB::table('activities')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => auth()->id(),
                'action' => 'return_processed',
                'description' => "Returned {$request->quantity} units of {$product->name} (Sale ID: {$request->sale_id})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode($request->only('reason', 'refund_amount')),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('manager.returns.index')->with('success', 'Item returned and restocked successfully.');
    }
}
