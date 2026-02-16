<?php
require 'vendor/autoload.php';
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Google\Auth\Middleware\AuthTokenMiddleware;

try {
    $path = 'storage/app/firebase-auth.json';
    $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
    $creds = new ServiceAccountCredentials($scopes, $path);

    $middleware = new AuthTokenMiddleware($creds);
    $stack = HandlerStack::create();
    $stack->push($middleware);

    $client = new Client([
        'handler' => $stack,
        'auth' => 'google_auth'
    ]);

    $token = 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQsQ-VcceglIV8_UeIxu0eWffcCDTJoh5pdPH_xAJl8FcTOogYqkFPz6tjMphS4MuR7FQmwj2PObOkqHJg_sFZyMZfJwrB5IAg';
    $projectId = 'flash-ship-6a0c1';

    echo "Sending via Guzzle...\n";
    $response = $client->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
        'json' => [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => 'Guzzle Test',
                    'body' => 'Success if you see this'
                ]
            ]
        ]
    ]);

    echo "SUCCESS! Response: " . $response->getBody() . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
