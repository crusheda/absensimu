<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FirebaseService
{
    private string $projectId;
    private string $clientEmail;
    private string $privateKey;

    public function __construct()
    {
        $firebaseCredentials = json_decode(
            Storage::get('firebase/fcmabsensi-firebase-adminsdk-fbsvc-38a62e7c46.json'),
            true
        );

        $this->projectId = $firebaseCredentials['project_id'];
        $this->clientEmail = $firebaseCredentials['client_email'];
        $this->privateKey = $firebaseCredentials['private_key'];
    }

    /**
     * Mendapatkan access token untuk FCM.
     */
    private function getAccessToken(): ?string
    {
        $jwtHeader = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));

        $now = time();
        $jwtClaim = base64_encode(json_encode([
            'iss' => $this->clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600
        ]));

        $dataToSign = $jwtHeader . '.' . $jwtClaim;
        $signature = '';
        openssl_sign($dataToSign, $signature, $this->privateKey, 'SHA256');
        $jwtSignature = base64_encode($signature);

        $jwt = $jwtHeader . '.' . $jwtClaim . '.' . $jwtSignature;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json()['access_token'] ?? null;
    }

    /**
     * Kirim notifikasi ke device tertentu menggunakan token login.
     *
     * @param array $tokens Array token device yang valid
     * @param string $title Judul notifikasi
     * @param string $body Isi notifikasi
     * @param array $data Data tambahan
     * @return array Hasil response FCM per token
     */
    public function sendNotification(array $tokens, string $title, string $body, array $data = []): array
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return ['error' => 'Failed to get access token'];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $responses = [];

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'data' => array_merge($data, [
                        'title' => $title,
                        'body' => $body,
                    ]),
                ]
            ];

            $responses[] = Http::withToken($accessToken)
                ->post($url, $payload)
                ->json();
        }

        return $responses;
    }
}
