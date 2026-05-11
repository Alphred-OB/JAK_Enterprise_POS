<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['user', 'product'])->latest();

        // Filter by Date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Action Type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $activities = $query->paginate(25)->withQueryString();
        
        $users = User::orderBy('name')->get();
        $actionTypes = Activity::select('action')->distinct()->pluck('action');

        return view('manager.activities.index', compact('activities', 'users', 'actionTypes'));
    }

    public function flagged(Request $request)
    {
        $query = Activity::with(['user', 'product'])
            ->whereIn('action', ['stock_adjusted', 'sale_cancelled', 'discount_applied', 'price_changed', 'issue_reported', 'inventory_conflict'])
            ->latest();

        // Apply same filters to flagged view
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $activities = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get();
            
        return view('manager.activities.flagged', compact('activities', 'users'));
    }

    public function reportIssue(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
            'urgency' => 'required|in:low,medium,high,critical'
        ]);

        Activity::log(
            'issue_reported',
            'Reported a problem: ' . \Illuminate\Support\Str::limit($request->description, 50),
            [
                'full_description' => $request->description,
                'urgency' => $request->urgency
            ]
        );

        return back()->with('success', 'Your issue has been reported and logged for management review.');
    }
}
