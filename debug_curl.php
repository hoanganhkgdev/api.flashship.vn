<?php
require 'vendor/autoload.php';
use Google\Auth\Credentials\ServiceAccountCredentials;

$path = 'storage/app/firebase-auth.json';
$scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
$creds = new ServiceAccountCredentials($scopes, $path);
$tokenData = $creds->fetchAuthToken();
$accessToken = $tokenData['access_token'];

$token = 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQs (truncated)';
$projectId = 'flash-ship-6a0c1';

$url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
$data = json_encode([
    'message' => [
        'token' => 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQsQ-VcceglIV8_UeIxu0eWffcCDTJoh5pdPH_xAJl8FcTOogYqkFPz6tjMphS4MuR7FQmwj2PObOkqHJg_sFZyMZfJwrB5IAg',
        'notification' => ['title' => 'Curl Test', 'body' => 'Success']
    ]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

echo "Sending via CURL...\n";
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo "HTTP Status: " . $info['http_code'] . "\n";
echo "Response: " . $response . "\n";
curl_close($ch);
