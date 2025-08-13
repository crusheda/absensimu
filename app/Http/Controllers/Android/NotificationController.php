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
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        // Ambil semua token unik dari tabel fcm_tokens
        $tokens = FcmToken::pluck('token')->unique()->toArray();

        if (empty($tokens)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada device untuk dikirimi notifikasi'
            ]);
        }

        // Kirim notifikasi per token
        $result = $firebase->sendNotification($tokens, $request->title, $request->body, [
            'type' => 'broadcast'
        ]);

        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }
}
