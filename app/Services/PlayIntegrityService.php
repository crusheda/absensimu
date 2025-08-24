<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google_Service_Playintegrity;

class PlayIntegrityService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setAuthConfig(storage_path('app/keys/fcmabsensi-11e43a71d377.json'));
        $this->client->addScope('https://www.googleapis.com/auth/playintegrity');

        $this->service = new Google_Service_Playintegrity($this->client);
    }

    /**
     * Verifikasi Play Integrity token dari aplikasi
     */
    public function verifyToken(string $token): array
    {
        // request ke Play Integrity API
        $requestBody = new \Google_Service_Playintegrity_GooglePlayIntegrityV1DecodeIntegrityTokenRequest();
        $requestBody->setIntegrityToken($token);

        $response = $this->service->v1->decodeIntegrityToken('projects/' . env('PLAY_PROJECT_NUMBER'), $requestBody);

        return $response->getTokenPayloadExternal(); // array info token
    }
}
