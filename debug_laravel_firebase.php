<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $messaging = app('firebase.messaging');
    echo "Firebase Messaging Instance Class: " . get_class($messaging) . "\n";

    // Check the config directly from Laravel's perspective
    echo "Config FIREBASE_CREDENTIALS: " . config('firebase.projects.app.credentials') . "\n";
    echo "Env FIREBASE_CREDENTIALS: " . env('FIREBASE_CREDENTIALS') . "\n";

    // Try a simple operation
    $messaging->validateRegistrationTokens(['invalid-token']);
    echo "Check passed (auth worked).\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
