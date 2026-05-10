<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->filled('end_date') ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

        $fileName = 'sales_export_' . now()->format('Y-m-d_H-i') . '.csv';
        
        $query = Sale::with(['user', 'items.product'])->latest();
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $sales = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Receipt #', 'Date', 'Cashier', 'Payment Method', 'Subtotal', 'Discount', 'Total', 'Items Sold'];

        $callback = function() use($sales, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($sales as $sale) {
                $items = $sale->items->map(function($item) {
                    return ($item->product->name ?? 'Deleted') . " (x" . $item->qty . ")";
                })->implode('; ');

                fputcsv($file, [
                    $sale->receipt_number,
                    $sale->created_at->format('Y-m-d H:i'),
                    $sale->user->name ?? 'System',
                    strtoupper($sale->payment_method),
                    number_format($sale->subtotal, 2),
                    number_format($sale->discount, 2),
                    number_format($sale->total, 2),
                    $items
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function products()
    {
        $fileName = 'inventory_export_' . now()->format('Y-m-d_H-i') . '.csv';
        $products = Product::with('category')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Name', 'SKU', 'Barcode', 'Category', 'Cost Price', 'Selling Price', 'Wholesale Price', 'Stock Level', 'Min Threshold', 'Status'];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->barcode,
                    $product->category->name ?? 'General',
                    number_format($product->cost_price, 2),
                    number_format($product->selling_price, 2),
                    number_format($product->wholesale_price, 2),
                    $product->stock_quantity,
                    $product->low_stock_threshold,
                    $product->is_active ? 'Active' : 'Inactive'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function expenses(Request $request)
    {
        $startDate = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->filled('end_date') ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

        $fileName = 'expenses_export_' . now()->format('Y-m-d_H-i') . '.csv';
        
        $query = Expense::with('user')->latest();
        if ($startDate && $endDate) {
            $query->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        }
        $expenses = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Date', 'Description', 'Category', 'Amount', 'Recorded By'];

        $callback = function() use($expenses, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->expense_date,
                    $expense->description,
                    $expense->category,
                    number_format($expense->amount, 2),
                    $expense->user->name ?? 'System'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function staff()
    {
        $fileName = 'staff_export_' . now()->format('Y-m-d_H-i') . '.csv';
        $users = User::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Name', 'Email', 'Role', 'Status', 'Joined Date'];

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    strtoupper($user->role),
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
