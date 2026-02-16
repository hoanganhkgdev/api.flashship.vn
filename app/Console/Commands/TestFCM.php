<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\FCMService;

class TestFCM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:test {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test FCM notification to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User #{$userId} not found!");
                return;
            }
            if (!$user->fcm_token) {
                $this->error("User #{$userId} ({$user->name}) does not have an FCM token!");
                return;
            }
            $tokens = [$user->fcm_token];
            $this->info("Sending test notification to: {$user->name}");
        } else {
            $tokens = User::where('role', 'driver')
                ->whereNotNull('fcm_token')
                ->whereHas('driver', function ($query) {
                    $query->where('status', 'online');
                })
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) {
                $this->error("No ONLINE drivers found with FCM tokens!");
                $this->line("Make sure you have switched to 'Online' mode in the app.");
                return;
            }
            $this->info("Sending test notification to " . count($tokens) . " ONLINE devices...");
        }

        $result = FCMService::send(
            $tokens,
            'FlashShip Test Notification 🚀',
            'This is a test notification from the backend system. Time: ' . now()->format('H:i:s'),
            ['type' => 'test', 'click_action' => 'FLUTTER_NOTIFICATION_CLICK']
        );

        if (isset($result['error'])) {
            $this->error("Critical Error: " . $result['error']);
        } else {
            $this->table(['Success', 'Fail'], [[$result['success'], $result['fail']]]);
            if ($result['success'] > 0) {
                $this->info("✅ Some notifications were sent successfully!");
            } else {
                $this->error("❌ No notifications were delivered. Check laravel.log for errors.");
            }
        }
    }
}
