<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

$path = '/Users/nguyenhoanganh/FlashShip/flashship-api/storage/app/firebase-auth.json';
$token = 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQsQ-VcceglIV8_UeIxu0eWffcCDTJoh5pdPH_xAJl8FcTOogYqkFPz6tjMphS4MuR7FQmwj2PObOkqHJg_sFZyMZfJwrB5IAg';

try {
    echo "Using path: $path\n";
    $factory = (new Factory)
        ->withServiceAccount($path);

    $messaging = $factory->createMessaging();
    $notification = Notification::create('Debug Title', 'Debug Body');
    $message = CloudMessage::withTarget('token', $token)
        ->withNotification($notification);

    echo "Sending...\n";
    $messaging->send($message);
    echo "SUCCESS!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
