<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user'])->latest()->paginate(15);
        return view('manager.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('manager.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $totalCost = 0;
            $reference = 'PO-' . strtoupper(Str::random(6)) . '-' . date('dm');

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => $request->user()->id,
                'reference_number' => $reference,
                'total_cost' => 0, // Will calculate below
                'notes' => $request->notes,
                'status' => 'received'
            ]);

            // Batch-load all products in one query instead of one per item
            $productIds = collect($request->items)->pluck('product_id')->unique()->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $purchaseItemsInsert = [];
            $stockUpdates = [];

            foreach ($request->items as $itemData) {
                $itemTotal  = $itemData['quantity'] * $itemData['unit_cost'];
                $totalCost += $itemTotal;

                $purchaseItemsInsert[] = [
                    'purchase_id' => $purchase->id,
                    'product_id'  => $itemData['product_id'],
                    'quantity'    => $itemData['quantity'],
                    'unit_cost'   => $itemData['unit_cost'],
                    'total_cost'  => $itemTotal,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                // Accumulate quantities so duplicate product lines in the same
                // request are summed rather than overwritten.
                if (isset($stockUpdates[$itemData['product_id']])) {
                    $stockUpdates[$itemData['product_id']]['qty'] += $itemData['quantity'];
                    $stockUpdates[$itemData['product_id']]['cost_price'] = $itemData['unit_cost'];
                } else {
                    $stockUpdates[$itemData['product_id']] = [
                        'qty'        => $itemData['quantity'],
                        'cost_price' => $itemData['unit_cost'],
                    ];
                }
            }

            // Single insert for all purchase items
            PurchaseItem::insert($purchaseItemsInsert);

            // Update each product's stock and cost price (still one query per product, but no extra find())
            foreach ($products as $product) {
                $update = $stockUpdates[$product->id];
                $product->increment('stock_quantity', $update['qty']);
                $product->update(['cost_price' => $update['cost_price']]);
            }

            $purchase->update(['total_cost' => $totalCost]);

            // Create an Expense record for this purchase
            Expense::create([
                'expense_date' => now()->format('Y-m-d'),
                'category' => 'Cost of Goods',
                'amount' => $totalCost,
                'description' => "Supplier Restock: {$reference}",
                'user_id' => $request->user()->id
            ]);

            DB::commit();

            return redirect()->route('manager.purchases.index')->with('success', 'Stock-in recorded and inventory updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Purchase recording failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to record purchase. Please try again.')->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['items.product', 'supplier', 'user']);
        return view('manager.purchases.show', compact('purchase'));
    }
}
