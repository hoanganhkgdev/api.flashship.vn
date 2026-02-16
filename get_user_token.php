<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(2);
echo "User: " . $user->name . "\n";
echo "FCM Token: " . ($user->fcm_token ?? 'NULL') . "\n";
echo "Updated At: " . $user->updated_at . "\n";
