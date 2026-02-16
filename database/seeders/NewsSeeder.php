<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $newsItems = [
            [
                'title' => 'Cập nhật tính năng FlashShip Pay mới',
                'description' => 'Thanh toán nhanh hơn, bảo mật hơn với OTP và xác thực sinh trắc học',
                'content' => 'FlashShip Pay đã được nâng cấp với nhiều tính năng bảo mật mới bao gồm OTP, xác thực sinh trắc học (vân tay, Face ID), và mã hóa giao dịch end-to-end. Giờ đây bạn có thể thanh toán nhanh chóng và an toàn hơn bao giờ hết.',
                'image' => 'https://img.freepik.com/free-vector/digital-wallet-concept-illustration_114360-7053.jpg',
                'type' => 'news',
                'is_active' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Chào Xuân Giáp Thìn - Lì xì cực đỉnh',
                'description' => 'Hàng ngàn voucher đang chờ bạn khám phá. Giảm giá lên đến 50%',
                'content' => 'Mừng Xuân Giáp Thìn, FlashShip tung ra chương trình khuyến mãi lớn nhất trong năm với hàng ngàn voucher giảm giá lên đến 50%. Đặc biệt, mỗi ngày đầu năm sẽ có lì xì may mắn trị giá đến 500.000đ cho 100 khách hàng đầu tiên.',
                'image' => 'https://static-images.vnncdn.net/files/publish/2023/1/18/dat-mon-khai-xuan-1087.jpg',
                'type' => 'promotion',
                'is_active' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'Giảm 30% cho đơn hàng đầu tiên',
                'description' => 'Người dùng mới nhận ngay voucher giảm 30% tối đa 50.000đ',
                'content' => 'Chào mừng bạn đến với FlashShip! Đặt đơn hàng đầu tiên của bạn ngay hôm nay để nhận voucher giảm giá 30% (tối đa 50.000đ). Ưu đãi có hiệu lực trong vòng 7 ngày kể từ ngày đăng ký.',
                'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=800',
                'type' => 'promotion',
                'is_active' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subWeek(),
            ],
            [
                'title' => 'Mở rộng khu vực giao hàng đến 5 tỉnh mới',
                'description' => 'FlashShip giờ đây có mặt tại Cần Thơ, An Giang, Kiên Giang, Vĩnh Long và Đồng Tháp',
                'content' => 'Chúng tôi vui mừng thông báo FlashShip đã mở rộng dịch vụ giao hàng đến 5 tỉnh miền Tây: Cần Thơ, An Giang, Kiên Giang, Vĩnh Long và Đồng Tháp. Đặt hàng ngay để nhận ưu đãi giảm phí ship 50% trong tháng đầu tiên.',
                'image' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800',
                'type' => 'news',
                'is_active' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subWeeks(2),
            ],
            [
                'title' => 'Freeship cho đơn hàng từ 99k',
                'description' => 'Miễn phí vận chuyển cho tất cả đơn hàng từ 99.000đ',
                'content' => 'Bắt đầu từ hôm nay, tất cả đơn hàng có giá trị từ 99.000đ trở lên sẽ được miễn phí vận chuyển. Không giới hạn số lần sử dụng, áp dụng cho tất cả các dịch vụ.',
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800',
                'type' => 'promotion',
                'is_active' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Giờ vàng giảm giá - Mỗi ngày 18h-20h',
                'description' => 'Flash sale 2 tiếng mỗi ngày với giảm giá lên đến 40%',
                'content' => 'Đừng bỏ lỡ Giờ Vàng Giảm Giá từ 18h-20h mỗi ngày. Hàng trăm món ăn, đồ uống giảm giá lên đến 40%. Set báo thức để không bỏ lỡ nhé!',
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800',
                'type' => 'promotion',
                'is_active' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDay(),
            ],
        ];

        foreach ($newsItems as $item) {
            News::create($item);
        }

        $this->command->info('✅ Created ' . count($newsItems) . ' news items successfully!');
    }
}
