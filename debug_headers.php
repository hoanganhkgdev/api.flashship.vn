<?php
require 'vendor/autoload.php';
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\Middleware;

try {
    $path = 'storage/app/firebase-auth.json';
    $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
    $creds = new ServiceAccountCredentials($scopes, $path);

    $stack = HandlerStack::create();
    $stack->push(new AuthTokenMiddleware($creds));

    // Debug middleware to see headers
    $stack->push(Middleware::mapRequest(function ($request) {
        echo "Headers: " . json_encode($request->getHeaders()) . "\n";
        return $request;
    }));

    $client = new Client(['handler' => $stack]);

    $token = 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQsQ-VcceglIV8_UeIxu0eWffcCDTJoh5pdPH_xAJl8FcTOogYqkFPz6tjMphS4MuR7FQmwj2PObOkqHJg_sFZyMZfJwrB5IAg';
    $projectId = 'flash-ship-6a0c1';

    $client->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
        'json' => ['message' => ['token' => $token, 'notification' => ['title' => 'T', 'body' => 'B']]]
    ]);

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
