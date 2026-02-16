<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

try {
    $path = 'storage/app/firebase-auth.json';
    $token = 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQsQ-VcceglIV8_UeIxu0eWffcCDTJoh5pdPH_xAJl8FcTOogYqkFPz6tjMphS4MuR7FQmwj2PObOkqHJg_sFZyMZfJwrB5IAg';

    $factory = (new Factory)->withServiceAccount($path);
    $messaging = $factory->createMessaging();

    echo "Attempting to send to REAL token...\n";
    $message = CloudMessage::withTarget('token', $token)
        ->withNotification(\Kreait\Firebase\Messaging\Notification::create('Debug Test', 'Body'));

    $messaging->send($message);
    echo "SENT SUCCESSFULLY!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
