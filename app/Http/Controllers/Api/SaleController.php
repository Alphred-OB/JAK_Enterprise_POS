<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('items.product', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($sales);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
            'payment_method' => 'required|string'
        ]);

        try {
            // Check for open shift
            $shift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
            if (!$shift) {
                return response()->json([
                    'success' => false,
                    'message' => 'No open shift found. Please open a shift before selling.'
                ], 403);
            }

            return DB::transaction(function () use ($request, $shift) {
                // 1. Create the Sale
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'shift_id' => $shift->id,
                    'customer_id' => $request->customer_id ?? null,
                    'receipt_number' => 'REC-' . strtoupper(Str::random(8)),
                    'subtotal' => $request->subtotal,
                    'discount' => $request->discount ?? 0,
                    'total' => $request->total,
                    'payment_method' => $request->payment_method,
                    'status' => 'completed',
                    'cash_received' => $request->payment_method === 'debt' ? 0 : $request->total, 
                    'change_amount' => 0
                ]);

                if ($request->payment_method === 'debt' && $request->customer_id) {
                    $customer = \App\Models\Customer::find($request->customer_id);
                    if ($customer) {
                        $customer->increment('total_debt', $request->total);
                        \App\Models\Activity::log('debt_issued', "GH₵ {$request->total} credit sale issued to {$customer->name} (Sale {$sale->receipt_number})", [
                            'customer_id' => $customer->id,
                            'sale_id' => $sale->id,
                            'amount' => $request->total
                        ]);
                    }
                }

                \App\Models\Activity::log('sale_created', "Processed sale {$sale->receipt_number} for total " . $request->total, [
                    'sale_id' => $sale->id,
                    'receipt_number' => $sale->receipt_number,
                    'total' => $request->total
                ]);

                if ($sale->discount > 0) {
                    \App\Models\Activity::log('discount_applied', "Discount of GH₵ {$sale->discount} given on sale {$sale->receipt_number}", [
                        'sale_id' => $sale->id,
                        'discount' => $sale->discount,
                        'total' => $sale->total
                    ]);
                }

                    // 2. Create Sale Items and Update Stock
                    foreach ($request->items as $item) {
                        $product = Product::findOrFail($item['id']);
                        
                        $status = 'normal';
                        $conflictNote = null;

                        // Check if this sale causes negative stock (Potential sync conflict)
                        if ($product->stock_quantity < $item['qty']) {
                            $status = 'conflict';
                            $conflictNote = "Stock discrepancy during sync: Item sold when system stock was {$product->stock_quantity}. Resulting in negative stock.";
                            
                            \App\Models\Activity::log('inventory_conflict', "Stock conflict for '{$product->name}' during sync. Sold {$item['qty']} while only {$product->stock_quantity} available.", [
                                'product_id' => $product->id,
                                'sale_id' => $sale->id,
                                'receipt_number' => $sale->receipt_number,
                                'available' => $product->stock_quantity,
                                'sold' => $item['qty']
                            ]);
                        }

                        $sale->items()->create([
                            'product_id' => $product->id,
                            'cost_price' => $product->cost_price,
                            'quantity' => $item['qty'],
                            'unit_price' => $item['selling_price'],
                            'total' => $item['selling_price'] * $item['qty'],
                            'status' => $status,
                            'conflict_note' => $conflictNote
                        ]);

                        // Deduct from Product Stock
                        if ($product) {
                            $product->decrement('stock_quantity', $item['qty']);
                        }
                    }

                return response()->json([
                    'success' => true,
                    'message' => 'Sale processed successfully',
                    'sale' => $sale->load('items.product'),
                    'receipt_number' => $sale->receipt_number
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process sale: ' . $e->getMessage()
            ], 500);
        }
    }
}
