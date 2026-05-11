<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Activity;
use Illuminate\Http\Request;

class InventoryConflictController extends Controller
{
    public function index()
    {
        $conflicts = SaleItem::with(['product', 'sale.user'])
            ->where('status', 'conflict')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manager.inventory.conflicts', compact('conflicts'));
    }

    public function resolve(Request $request, SaleItem $item)
    {
        $request->validate([
            'actual_stock' => 'required|integer|min:0',
            'resolution_note' => 'required|string|max:500'
        ]);

        $product = $item->product;
        $oldStock = $product->stock_quantity;
        
        // Update product stock to actual reality found on shelf
        $product->update([
            'stock_quantity' => $request->actual_stock
        ]);

        // Mark item as resolved
        $item->update([
            'status' => 'resolved',
            'conflict_note' => $item->conflict_note . "\n\nRESOLVED: " . $request->resolution_note
        ]);

        Activity::log('conflict_resolved', "Inventory conflict for '{$product->name}' resolved. Physical count set to {$request->actual_stock} (was {$oldStock}).", [
            'product_id' => $product->id,
            'old_stock' => $oldStock,
            'new_stock' => $request->actual_stock,
            'note' => $request->resolution_note
        ]);

        return back()->with('success', 'Inventory conflict resolved and stock updated to physical count.');
    }
}
