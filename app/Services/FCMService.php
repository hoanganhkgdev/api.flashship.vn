<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class FCMService
{
    public static function send($tokens, $title, $body, $data = [])
    {
        if (empty($tokens))
            return ['success' => 0, 'fail' => 0];
        $tokens = is_array($tokens) ? $tokens : [$tokens];

        try {
            $path = storage_path('app/firebase-auth.json');
            $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
            $creds = new ServiceAccountCredentials($scopes, $path);
            $tokenData = $creds->fetchAuthToken(HttpHandlerFactory::build());
            $accessToken = $tokenData['access_token'];

            $projectId = 'flash-ship-app';
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $successCount = 0;
            $failCount = 0;

            foreach ($tokens as $token) {
                // Simplified payload - let FCM handle the environment routing
                $payload = json_encode([
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => array_map('strval', $data)
                    ]
                ]);

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200) {
                    $successCount++;
                    Log::info("✅ FCM Success: " . substr($token, 0, 10));
                } else {
                    $failCount++;
                    Log::error("❌ FCM Failure HTTP $httpCode: " . $response);
                }
            }

            return ['success' => $successCount, 'fail' => $failCount];

        } catch (\Exception $e) {
            Log::error("❌ FCM Critical Error: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public static function notifyDrivers($title, $body, $data = [])
    {
        $tokens = \App\Models\User::where('role', 'driver')
            ->whereNotNull('fcm_token')
            ->whereHas('driver', function ($query) {
                $query->where('status', 'online');
            })
            ->pluck('fcm_token')
            ->toArray();

        return self::send($tokens, $title, $body, $data);
    }
}
