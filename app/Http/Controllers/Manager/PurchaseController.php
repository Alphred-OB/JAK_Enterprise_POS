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

            foreach ($request->items as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['unit_cost'];
                $totalCost += $itemTotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $itemTotal
                ]);

                // Update product stock
                $product = Product::find($itemData['product_id']);
                $product->increment('stock_quantity', $itemData['quantity']);
                
                // Optionally update the product's default cost_price based on the latest purchase
                $product->update(['cost_price' => $itemData['unit_cost']]);
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
            return back()->with('error', 'Error recording purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['items.product', 'supplier', 'user']);
        return view('manager.purchases.show', compact('purchase'));
    }
}
