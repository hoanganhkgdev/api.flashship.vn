<?php
require 'vendor/autoload.php';
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

try {
    $path = 'storage/app/firebase-auth.json';
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
    $creds = new ServiceAccountCredentials($scopes, $path);

    echo "Fetching token manually...\n";
    $tokenData = $creds->fetchAuthToken();
    $accessToken = $tokenData['access_token'];
    echo "Token length: " . strlen($accessToken) . "\n";
    echo "Token start: " . substr($accessToken, 0, 10) . "...\n";

    $client = new Client();
    $token = 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQsQ-VcceglIV8_UeIxu0eWffcCDTJoh5pdPH_xAJl8FcTOogYqkFPz6tjMphS4MuR7FQmwj2PObOkqHJg_sFZyMZfJwrB5IAg';
    $projectId = 'flash-ship-6a0c1';

    echo "Sending with Manual Header...\n";
    $response = $client->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => 'Manual Test',
                    'body' => 'Success if received'
                ]
            ]
        ]
    ]);

    echo "SUCCESS! Response: " . $response->getBody() . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
