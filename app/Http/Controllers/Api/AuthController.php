<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4'
        ]);

        $user = User::where('pin_code', $request->pin)
            ->whereIn('role', ['manager', 'admin'])
            ->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'PIN Verified',
                'manager' => $user->name
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid Manager PIN'
        ], 401);
    }
}
