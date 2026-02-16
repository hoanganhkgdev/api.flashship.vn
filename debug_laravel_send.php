<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $messaging = app('firebase.messaging');
    $token = 'f_Ex5krULUs6pj69nXL-Np:APA91bHHHTpaGUqhP6lkIuQsQ-VcceglIV8_UeIxu0eWffcCDTJoh5pdPH_xAJl8FcTOogYqkFPz6tjMphS4MuR7FQmwj2PObOkqHJg_sFZyMZfJwrB5IAg';

    $notification = \Kreait\Firebase\Messaging\Notification::create('Debug', 'Body');
    $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $token)
        ->withNotification($notification);

    echo "Sending REAL message via Laravel app messaging...\n";
    $messaging->send($message);
    echo "SENT SUCCESSFULLY (Laravel context)!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
