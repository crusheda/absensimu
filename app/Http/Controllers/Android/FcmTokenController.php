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
            'device_id'  => 'required|string',
            'platform' => 'required|string',      // 'android' / 'ios'
            'os_version' => 'nullable|string',    // versi OS
            'model' => 'nullable|string',         // model device
            'is_rooted' => 'nullable|string',         // model device
        ]);

        $userId = $request->user_id;
        // $user = Auth::user();

        // 🔒 cek apakah user sudah punya device aktif
        $existingDevice = FcmToken::where('user_id', $userId)
            ->where('is_active', 1)
            ->first();

        if ($existingDevice) {
            if ($existingDevice->device_id !== $request->device_id) {
                // device berbeda → tolak binding
                return response()->json([
                    'success' => false,
                    'message' => 'Akun ini sudah terhubung dengan perangkat lain',
                ], 403);
            }

            // kalau device_id sama → update token dan info device
            $existingDevice->update([
                'token'        => $request->token,
                'platform'     => $request->platform,
                'os_version'   => $request->os_version,
                'model'        => $request->model,
                'is_rooted'    => $request->is_rooted,
                'last_login_at'=> now(),
                'is_active'    => 1,
            ]);
        } else {
            // kalau belum ada → daftarkan device baru
            FcmToken::updateOrCreate(
                [
                    'user_id'   => $userId,
                    'device_id' => $request->device_id,
                ],
                [
                    'token'        => $request->token,
                    'platform'     => $request->platform,
                    'os_version'   => $request->os_version,
                    'model'        => $request->model,
                    'is_rooted'    => $request->is_rooted,
                    'is_active'    => 1,
                    'last_login_at'=> now(),
                ]
            );
        }

        return response()->json(['message' => 'FCM token saved successfully']);
    }

    public function removeToken(Request $request) {
        $request->validate([
            'user_id'   => 'required|integer',
            'device_id' => 'required|string',
        ]);

        // nonaktifkan device
        FcmToken::where('user_id', $request->user_id)
            ->where('device_id', $request->device_id)
            ->update(['is_active' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Token removed successfully',
        ]);
    }
}
