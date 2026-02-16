<?php
require 'vendor/autoload.php';
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

try {
    $path = 'storage/app/firebase-auth.json';
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

    $creds = new ServiceAccountCredentials($scopes, $path);
    echo "Fetching token...\n";
    $token = $creds->fetchAuthToken(HttpHandlerFactory::build());

    if (isset($token['access_token'])) {
        echo "SUCCESS! Access Token: " . substr($token['access_token'], 0, 10) . "...\n";
    } else {
        echo "FAILED! Response: " . json_encode($token) . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
