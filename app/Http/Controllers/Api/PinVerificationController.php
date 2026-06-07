<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PinVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'pin'    => 'required|string|size:4',
            'action' => 'required|string',
        ]);

        // Use lazy() so we stop at the first match without loading all managers into memory
        $manager = User::whereIn('role', ['manager', 'admin'])
            ->where('is_active', true)
            ->whereNotNull('pin_code')
            ->lazy()
            ->first(fn($u) => Hash::check($request->pin, $u->pin_code));

        if (!$manager) {
            return response()->json(['success' => false, 'message' => 'Invalid Manager PIN.'], 403);
        }

        DB::table('activities')->insert([
            'id'          => \Illuminate\Support\Str::uuid(),
            'user_id'     => $manager->id,
            'action'      => 'manager_override',
            'description' => "Manager {$manager->name} authorized {$request->action} via PIN",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'metadata'    => json_encode(['action' => $request->action]),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
