<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::with('user')->latest('opened_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('opened_at', $request->date);
        }

        $shifts = $query->paginate(20)->withQueryString();

        return view('manager.shifts.index', compact('shifts'));
    }
}
