<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FcmToken;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function broadcast(Request $request, FirebaseService $firebase)
    {
        $validated = $request->validate([
            'title' => 'required|string', // |max:255
            'body'  => 'required|string',
        ]);

        // Ambil semua token unik dari tabel fcm_tokens
        // $tokens = FcmToken::distinct()->pluck('token')->toArray();
        $tokens = FcmToken::where('status', 1)
            ->where('accepted', 1)
            ->withoutTrashed()
            ->distinct()
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada device aktif untuk menerima notifikasi',
            ], 404);
        }

        // Kirim notifikasi
        $result = $firebase->sendNotification(
            $tokens,
            $validated['title'],
            $validated['body'],
            ['type' => 'broadcast']
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi',
                'errors'  => $result['errors'] ?? [],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dikirim ke semua Device Aktif',
            'result'  => $result,
        ]);
    }
}
