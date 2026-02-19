<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\News::truncate();

        $news = [
            [
                'title' => 'FlashShip chính thức ra mắt phiên bản mới',
                'description' => 'Trải nghiệm giao hàng siêu tốc và đặt đồ ăn cực nhanh với giao diện hoàn toàn mới.',
                'content' => 'Chúng tôi vui mừng thông báo FlashShip đã chính thức cập nhật phiên bản mới với nhiều tính năng hấp dẫn. Giao diện được tối ưu hóa để người dùng có thể đặt đơn chỉ trong 3 bước. Ngoài ra, tốc độ xử lý đơn hàng cũng được cải thiện đáng kể.',
                'image' => 'https://images.unsplash.com/photo-1586769852836-bc069f19e1b6?q=80&w=1000&auto=format&fit=crop',
                'type' => 'announcement',
                'is_active' => true,
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Mẹo tiết kiệm chi phí khi đặt đơn FlashShip',
                'description' => 'Sử dụng ví FlashShip Pay và tích điểm FlashPoints để nhận ưu đãi lên đến 50%.',
                'content' => 'Bạn có biết mình có thể tiết kiệm rất nhiều tiền nếu biết cách sử dụng FlashPoints? Mỗi đơn hàng hoàn thành sẽ giúp bạn tích lũy điểm thưởng để đổi lấy các mã giảm giá cho lần đặt sau. Hãy kết hợp cùng ví điện tử để nhận thêm các ưu đãi hoàn tiền cực hời.',
                'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1000&auto=format&fit=crop',
                'type' => 'news',
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),
            ],
            [
                'title' => 'FlashShip đồng hành cùng cộng đồng',
                'description' => 'Chương trình thiện nguyện hỗ trợ các hoàn cảnh khó khăn tại địa phương.',
                'content' => 'Trong tháng này, đội ngũ tài xế FlashShip đã cùng nhau tham gia hoạt động thiện nguyện, mang những phần quà ý nghĩa đến các mái ấm. Chúng tôi cam kết không chỉ mang lại dịch vụ tốt nhất mà còn góp phần xây dựng xã hội tốt đẹp hơn.',
                'image' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=1000&auto=format&fit=crop',
                'type' => 'news',
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),
            ],
            [
                'title' => 'Cập nhật tính năng theo dõi tài xế thời gian thực',
                'description' => 'Giờ đây bạn có thể biết chính xác tài xế đang ở đâu trên bản đồ.',
                'content' => 'Tính năng theo dõi đơn hàng đã được nâng cấp. Bạn có thể thấy vị trí của tài xế di chuyển từng mét một trên ứng dụng, giúp bạn chủ động hơn trong việc nhận hàng.',
                'image' => 'https://images.unsplash.com/photo-1526628953301-3e589a6a8b74?q=80&w=1000&auto=format&fit=crop',
                'type' => 'announcement',
                'is_active' => true,
                'is_featured' => true,
                'published_at' => now(),
            ],
        ];

        foreach ($news as $item) {
            \App\Models\News::create($item);
        }
    }
}
