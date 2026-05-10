<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Sale;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function current()
    {
        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        return response()->json([
            'has_open_shift' => !!$shift,
            'shift' => $shift
        ]);
    }

    public function open(Request $request)
    {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0'
        ]);

        // Close any existing open shifts just in case
        Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->update([
                'status' => 'closed',
                'closed_at' => now(),
                'notes' => 'Auto-closed by new shift opening'
            ]);

        $shift = Shift::create([
            'user_id' => Auth::id(),
            'opening_cash' => $request->opening_cash,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        Activity::log('shift_opened', "Cashier opened a new shift with GH₵ {$request->opening_cash} in drawer", [
            'shift_id' => $shift->id,
            'opening_cash' => $request->opening_cash
        ]);

        return response()->json([
            'success' => true,
            'shift' => $shift
        ]);
    }

    public function close(Request $request)
    {
        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->firstOrFail();

        // Calculate expected totals from sales
        $sales = Sale::where('shift_id', $shift->id)->get();
        
        $expected_momo = $sales->where('payment_method', 'momo')->sum('total');
        $expected_card = $sales->where('payment_method', 'card')->sum('total');
        $expected_debt = $sales->where('payment_method', 'debt')->sum('total');
        $cash_sales = $sales->where('payment_method', 'cash')->sum('total');
        
        $expected_cash = $shift->opening_cash + $cash_sales;

        if ($request->has('preview')) {
            return response()->json([
                'success' => true,
                'summary' => [
                    'expected_cash' => $expected_cash,
                    'momo' => $expected_momo,
                    'card' => $expected_card,
                    'debt' => $expected_debt
                ]
            ]);
        }

        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $shift->update([
            'closing_cash' => $request->closing_cash,
            'expected_cash' => $expected_cash,
            'expected_momo' => $expected_momo,
            'expected_card' => $expected_card,
            'expected_debt' => $expected_debt,
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => $request->notes
        ]);

        Activity::log('shift_closed', "Cashier closed shift. Expected GH₵ {$expected_cash} cash, counted GH₵ {$request->closing_cash}", [
            'shift_id' => $shift->id,
            'expected_cash' => $expected_cash,
            'actual_cash' => $request->closing_cash,
            'difference' => $request->closing_cash - $expected_cash
        ]);

        return response()->json([
            'success' => true,
            'summary' => [
                'expected_cash' => $expected_cash,
                'actual_cash' => $request->closing_cash,
                'difference' => $request->closing_cash - $expected_cash,
                'momo' => $expected_momo,
                'card' => $expected_card,
                'debt' => $expected_debt
            ]
        ]);
    }
}
