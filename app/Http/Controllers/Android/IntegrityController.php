<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PlayIntegrityService;

class IntegrityController extends Controller
{
    protected $service;

    public function __construct(PlayIntegrityService $service)
    {
        $this->service = $service;
    }

    public function verify_integrity(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->input('token');

        try {
            // Panggil service
            $payload = $this->service->verifyToken($token);

            // Periksa app integrity
            $appVerdict = $payload['appIntegrity']['appRecognitionVerdict'] ?? 'UNKNOWN';
            $isValid = $appVerdict === 'PLAY_RECOGNIZED';

            return response()->json([
                'valid' => $isValid,
                'appVerdict' => $appVerdict,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
