<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FcmToken;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|string',      // 'android' / 'ios'
            'os_version' => 'nullable|string',    // versi OS
            'model' => 'nullable|string',         // model device
            'is_rooted' => 'nullable|string',         // model device
        ]);

        $userId = $request->user_id;
        // $user = Auth::user();

        // Simpan token unik per user
        FcmToken::updateOrCreate(
            [
                'user_id' => $userId,
                'token' => $request->token,
                'platform' => $request->platform,
                'os_version' => $request->os_version,
                'model' => $request->model,
                'is_rooted' => $request->is_rooted,
            ],
            [
                'token' => $request->token,
                'platform' => $request->platform,
                'os_version' => $request->os_version,
                'model' => $request->model,
                'is_rooted' => $request->is_rooted,
            ]
        );

        return response()->json(['message' => 'FCM token saved successfully']);
    }

    public function removeToken(Request $request) {
        $token = $request->token;
        FcmToken::where('token', $token)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token removed successfully',
        ]);
    }
}
