<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks for clearing data safely
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        Review::truncate();
        Order::truncate();

        // Re-enable after truncating
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Create some demo users
        $customerNames = [
            'Nguyễn Văn An',
            'Trần Thị Bình',
            'Lê Văn Cường',
            'Phạm Minh Đức',
            'Hoàng Anh Tú',
            'Đặng Quang Huy',
            'Bùi Tuyết Mai',
            'Vũ Hồng Nhung',
            'Lý Gia Kiệt',
            'Ngô Bảo Châu'
        ];

        $users = collect();
        foreach ($customerNames as $index => $name) {
            $users->push(User::firstOrCreate(
                ['email' => "user$index@example.com"],
                [
                    'name' => $name,
                    'phone' => '090123456' . $index,
                    'password' => bcrypt('password'),
                    'role' => 'customer'
                ]
            ));
        }

        $stores = Store::all();
        $foodService = Service::whereIn('slug', ['food', 'do-an', 'giao-do-an'])->first();
        $firstService = Service::first();

        $comments = [
            'Cơm tấm ở đây sườn nướng rất mềm và thơm, nước mắm vừa vị.',
            'Giao hàng nhanh, hộp cơm còn nóng hổi. Rất hài lòng.',
            'Tỉ lệ thịt và cơm rất hợp lý, bì chả cũng ngon nữa.',
            'Quán đóng gói rất cẩn thận, không bị đổ nước mắm ra ngoài.',
            'Món ăn ngon, giá cả phù hợp với chất lượng. 5 sao!',
            'Đồ ăn tươi ngon, trình bày đẹp mắt dù là đồ mang về.',
            'Vị đậm đà đúng chất Sài Gòn, rất thích món chả trứng ở đây.',
            'Nhân viên phục vụ nhanh nhẹn.',
            'Một trong những quán cơm tấm ngon nhất mình từng ăn.',
            'Giao hàng đúng giờ, anh shipper cực kỳ lịch sự.'
        ];

        foreach ($stores as $store) {
            // Create 5-10 reviews for each store
            $count = rand(5, 10);
            $totalRating = 0;
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();

                // Determine service ID
                $serviceId = $store->service_id;
                if (!$serviceId) {
                    $serviceId = $foodService ? $foodService->id : ($firstService ? $firstService->id : null);
                }

                if (!$serviceId)
                    continue;

                // Create a dummy order for this review
                $order = Order::create([
                    'user_id' => $user->id,
                    'store_id' => $store->id,
                    'service_id' => $serviceId,
                    'total_amount' => 50000,
                    'status' => 'completed',
                    'is_rated' => true,
                    'pickup_address' => $store->address,
                    'dropoff_address' => 'Sample Delivery Address',
                    'payment_method' => 'cash',
                ]);

                $rating = rand(4, 5);
                $totalRating += $rating;

                Review::create([
                    'store_id' => $store->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'rating' => $rating,
                    'comment' => $comments[array_rand($comments)],
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
            }

            // Update store's average rating
            if ($count > 0) {
                $store->update(['rating' => round($totalRating / $count, 1)]);
            }
        }
    }
}
