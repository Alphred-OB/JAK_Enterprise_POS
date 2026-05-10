<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\InventoryLog;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockController extends Controller
{
    /**
     * Show the Physical Stock Audit view.
     */
    public function audit(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        
        return view('manager.stock.audit', compact('products'));
    }

    /**
     * Process a physical stock audit discrepancy.
     */
    public function storeAudit(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'physical_count' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            $expectedCount = $product->stock_quantity;
            $actualCount = $request->physical_count;
            $discrepancy = $actualCount - $expectedCount;

            if ($discrepancy == 0) {
                return; // Nothing to do if count is perfect
            }

            // Update product stock
            $product->stock_quantity = $actualCount;
            $product->save();

            // Log to Inventory Ledger
            InventoryLog::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'change_quantity' => $discrepancy,
                'type' => 'adjustment',
                'reason' => 'Stock Audit: ' . $request->reason
            ]);

            // Log to Activity/Audit Trail
            Activity::log(
                'stock_audit',
                "Audited {$product->name}. Expected: {$expectedCount}, Actual: {$actualCount}. Discrepancy: {$discrepancy}.",
                [
                    'product_id' => $product->id,
                    'expected_count' => $expectedCount,
                    'physical_count' => $actualCount,
                    'discrepancy' => $discrepancy,
                    'reason' => $request->reason
                ]
            );
        });

        return redirect()->route('manager.stock.audit')->with('success', 'Stock audit recorded successfully.');
    }

    /**
     * Show the Bulk Stock-In (Supplier Delivery) view.
     */
    public function stockIn(Request $request)
    {
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        
        return view('manager.stock.in', compact('products', 'suppliers'));
    }

    /**
     * Process incoming supplier stock delivery.
     */
    public function storeStockIn(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            $quantityAdded = $request->quantity;

            // Update product stock
            $product->increment('stock_quantity', $quantityAdded);

            // Log to Inventory Ledger
            InventoryLog::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'change_quantity' => $quantityAdded,
                'type' => 'stock_in',
                'reason' => 'Supplier Delivery. Ref: ' . ($request->reference_number ?? 'N/A')
            ]);

            // Log to Activity/Audit Trail
            $supplier = Supplier::find($request->supplier_id);
            Activity::log(
                'stock_in',
                "Received {$quantityAdded} units of {$product->name} from {$supplier->name}.",
                [
                    'product_id' => $product->id,
                    'supplier_id' => $supplier->id,
                    'quantity_received' => $quantityAdded,
                    'reference_number' => $request->reference_number,
                    'notes' => $request->notes
                ]
            );
        });

        return redirect()->route('manager.stock.in')->with('success', 'Stock successfully received and added to inventory.');
    }
}
