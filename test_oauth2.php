<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;

try {
    $path = 'storage/app/firebase-auth.json';
    $factory = (new Factory)->withServiceAccount($path);

    // Attempt to get an access token for FCM scope
    $googleAuth = $factory->createGoogleAuth();
    $token = $googleAuth->fetchAuthToken();

    if (isset($token['access_token'])) {
        echo "Successfully fetched access token: " . substr($token['access_token'], 0, 15) . "...\n";
    } else {
        echo "Failed to fetch access token. Response: " . json_encode($token) . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
