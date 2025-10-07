<?php
namespace App\Services;

use Firebase\JWT\JWT;

class ZoomSdkService
{
    public function generateSignature($meetingNumber, $role = 0)
    {
        $sdkKey    = env('ZOOM_SDK_KEY');
        $sdkSecret = env('ZOOM_SDK_SECRET');
        $time      = time() * 1000 - 30000; // timestamp in ms

        $payload = [
            'sdkKey'   => $sdkKey,
            'mn'       => $meetingNumber,      // meeting number
            'role'     => $role,               // 0 = participant, 1 = host
            'iat'      => $time,
            'exp'      => $time + (2 * 60 * 60 * 1000), // 2 hours
            'tokenExp' => $time + (2 * 60 * 60 * 1000),
        ];

        return JWT::encode($payload, $sdkSecret, 'HS256');
    }
}
