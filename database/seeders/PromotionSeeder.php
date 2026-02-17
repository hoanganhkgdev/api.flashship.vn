<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = [
            [
                'code' => 'FLASHSHIP20',
                'title' => 'Chào mừng bạn mới',
                'description' => 'Giảm 20% cho đơn hàng đầu tiên (tối đa 50k)',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'min_order_amount' => 50000,
                'max_discount_amount' => 50000,
                'expires_at' => Carbon::now()->addMonths(3),
                'usage_limit' => 1000,
            ],
            [
                'code' => 'FREESHIP',
                'title' => 'Miễn phí vận chuyển',
                'description' => 'Giảm tối đa 15k phí vận chuyển cho đơn từ 100k',
                'discount_type' => 'fixed',
                'discount_value' => 15000,
                'min_order_amount' => 100000,
                'max_discount_amount' => 15000,
                'expires_at' => Carbon::now()->addMonths(1),
                'usage_limit' => 500,
            ],
            [
                'code' => 'GIAM10K',
                'title' => 'Giảm giá trực tiếp',
                'description' => 'Giảm ngay 10k cho mọi đơn hàng từ 50k',
                'discount_type' => 'fixed',
                'discount_value' => 10000,
                'min_order_amount' => 50000,
                'max_discount_amount' => 10000,
                'expires_at' => Carbon::now()->addWeeks(2),
                'usage_limit' => 200,
            ],
            [
                'code' => 'ANCALANG',
                'title' => 'Ăn cả làng cùng FlashFood',
                'description' => 'Giảm 10% cho đơn hàng lớn trên 500k',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_amount' => 500000,
                'max_discount_amount' => 100000,
                'expires_at' => Carbon::now()->addMonths(6),
                'usage_limit' => 100,
            ],
            [
                'code' => 'CHOIDEM',
                'title' => 'Cú đêm FlashShip',
                'description' => 'Giảm 15k cho đơn hàng từ 22h - 2h sáng',
                'discount_type' => 'fixed',
                'discount_value' => 15000,
                'min_order_amount' => 80000,
                'max_discount_amount' => 15000,
                'expires_at' => Carbon::now()->addMonths(2),
                'usage_limit' => null,
            ],
        ];

        foreach ($promotions as $promo) {
            Promotion::updateOrCreate(
                ['code' => $promo['code']],
                $promo
            );
        }
    }
}
