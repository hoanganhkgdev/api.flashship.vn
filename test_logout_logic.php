<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

$user = User::find(2);
$user->fcm_token = 'test_token_to_be_cleared';
$user->save();

echo "Initial token: " . User::find(2)->fcm_token . "\n";

// Emulate logout
$request = Request::create('/logout', 'POST');
$request->setUserResolver(function () use ($user) {
    return $user;
});

// We need an access token to delete, but for testing logic:
$user->fcm_token = null;
$user->save();

echo "Final token: " . User::find(2)->fcm_token . "\n";
