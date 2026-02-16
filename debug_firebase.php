<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;

try {
    $path = 'storage/app/firebase-auth.json';
    echo "Path: " . realpath($path) . "\n";
    $factory = (new Factory)->withServiceAccount($path);
    $messaging = $factory->createMessaging();
    echo "Messaging service created successfully.\n";

    // Try to get a token to see if auth works
    // (This might require more setup but let's see)
    echo "Attempting to send a dummy message to an invalid token...\n";
    $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQsQ-VcceglIV8_UeIxu0eWffcCDTJoh5pdPH_xAJl8FcTOogYqkFPz6tjMphS4MuR7FQmwj2PObOkqHJg_sFZyMZfJwrB5IAg')
        ->withNotification(\Kreait\Firebase\Messaging\Notification::create('Test', 'Body'));

    try {
        $messaging->send($message);
    } catch (\Kreait\Firebase\Exception\MessagingException $e) {
        echo "Caught expected delivery error: " . $e->getMessage() . "\n";
        // If it reaches here, auth WORKED (because it tried to deliver).
    }
} catch (\Exception $e) {
    echo "Auth Error: " . $e->getMessage() . "\n";
}
