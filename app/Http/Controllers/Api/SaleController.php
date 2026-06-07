<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Sale::with('items.product', 'user')->orderBy('created_at', 'desc');

        // Cashiers only see their own sales
        if ($user->role === 'cashier') {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|integer|min:1',
            'discount'       => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        try {
            $shift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
            if (!$shift) {
                return response()->json([
                    'success' => false,
                    'message' => 'No open shift found. Please open a shift before selling.'
                ], 403);
            }

            return DB::transaction(function () use ($request, $shift) {
                // Batch-load all products in one query — avoids N+1 (one query regardless of cart size)
                $productIds = collect($request->items)->pluck('id')->unique()->all();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                $subtotal = 0;
                $resolvedItems = [];
                foreach ($request->items as $item) {
                    $product = $products[$item['id']] ?? Product::findOrFail($item['id']);
                    $lineTotal = $product->selling_price * $item['qty'];
                    $subtotal += $lineTotal;
                    $resolvedItems[] = ['product' => $product, 'qty' => $item['qty'], 'lineTotal' => $lineTotal];
                }

                $discount = $request->discount ?? 0;
                $total = max(0, $subtotal - $discount);

                $sale = Sale::create([
                    'user_id'        => auth()->id(),
                    'shift_id'       => $shift->id,
                    'customer_id'    => $request->customer_id ?? null,
                    'receipt_number' => 'REC-' . strtoupper(Str::random(8)),
                    'subtotal'       => $subtotal,
                    'discount'       => $discount,
                    'total'          => $total,
                    'payment_method' => $request->payment_method,
                    'status'         => 'completed',
                    'cash_received'  => $request->payment_method === 'debt' ? 0 : $total,
                    'change_amount'  => 0,
                ]);

                if ($request->payment_method === 'debt' && $request->customer_id) {
                    $customer = \App\Models\Customer::find($request->customer_id);
                    if ($customer) {
                        $customer->increment('total_debt', $total);
                        \App\Models\Activity::log('debt_issued', "GH₵ {$total} credit sale issued to {$customer->name} (Sale {$sale->receipt_number})", [
                            'customer_id' => $customer->id,
                            'sale_id'     => $sale->id,
                            'amount'      => $total,
                        ]);
                    }
                }

                \App\Models\Activity::log('sale_created', "Processed sale {$sale->receipt_number} for total {$total}", [
                    'sale_id'        => $sale->id,
                    'receipt_number' => $sale->receipt_number,
                    'total'          => $total,
                ]);

                if ($sale->discount > 0) {
                    \App\Models\Activity::log('discount_applied', "Discount of GH₵ {$sale->discount} given on sale {$sale->receipt_number}", [
                        'sale_id'  => $sale->id,
                        'discount' => $sale->discount,
                        'total'    => $sale->total,
                    ]);
                }

                foreach ($resolvedItems as ['product' => $product, 'qty' => $qty, 'lineTotal' => $lineTotal]) {
                    $status = 'normal';
                    $conflictNote = null;

                    if ($product->stock_quantity < $qty) {
                        $status = 'conflict';
                        $conflictNote = "Stock discrepancy during sync: Item sold when system stock was {$product->stock_quantity}. Resulting in negative stock.";

                        \App\Models\Activity::log('inventory_conflict', "Stock conflict for '{$product->name}' during sync. Sold {$qty} while only {$product->stock_quantity} available.", [
                            'product_id'     => $product->id,
                            'sale_id'        => $sale->id,
                            'receipt_number' => $sale->receipt_number,
                            'available'      => $product->stock_quantity,
                            'sold'           => $qty,
                        ]);
                    }

                    $sale->items()->create([
                        'product_id'    => $product->id,
                        'cost_price'    => $product->cost_price,
                        'quantity'      => $qty,
                        'unit_price'    => $product->selling_price,
                        'total'         => $lineTotal,
                        'status'        => $status,
                        'conflict_note' => $conflictNote,
                    ]);

                    $product->decrement('stock_quantity', $qty);
                }

                return response()->json([
                    'success'        => true,
                    'message'        => 'Sale processed successfully',
                    'sale'           => $sale->load('items.product'),
                    'receipt_number' => $sale->receipt_number,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Sale processing failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to process sale. Please try again.',
            ], 500);
        }
    }
}
