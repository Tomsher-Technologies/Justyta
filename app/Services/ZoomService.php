<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZoomService
{
    protected $accountId;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->accountId = config('services.zoom.account_id');
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
    }

    protected function getAccessToken()
    {
        $response = Http::asForm()->withBasicAuth($this->clientId, $this->clientSecret)
            ->post("https://zoom.us/oauth/token", [
                'grant_type' => 'account_credentials',
                'account_id' => $this->accountId,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get Zoom access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    public function createMeeting($topic, $startTime, $duration, $userEmail = 'me')
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post("https://api.zoom.us/v2/users/{$userEmail}/meetings", [
                'topic'      => $topic,
                'type'       => 2, // scheduled meeting
                'start_time' => $startTime,
                'duration'   => $duration,
                'timezone'   => config('app.timezone', 'UTC'),
                'settings'   => [
                    'host_video' => true,
                    'participant_video' => true,
                    'waiting_room' => false,
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create Zoom meeting: ' . $response->body());
        }

        return $response->json();
    }
}
