<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string'
        ]);

        Activity::log('support_reported', "Support report submitted [{$request->category}]: " . $request->description, [
            'category' => $request->category,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Support report recorded'
        ]);
    }
}
