<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PinVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4',
            'action' => 'required|string'
        ]);

        $manager = User::where('pin_code', $request->pin)
            ->whereIn('role', ['manager', 'admin'])
            ->where('is_active', true)
            ->first();

        if (!$manager) {
            return response()->json(['success' => false, 'message' => 'Invalid Manager PIN.'], 403);
        }

        // Log the authorization
        DB::table('activities')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $manager->id,
            'action' => 'manager_override',
            'description' => "Manager {$manager->name} authorized {$request->action} via PIN",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => json_encode(['action' => $request->action]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
