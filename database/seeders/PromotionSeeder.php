<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Promotion::create([
            'code' => 'FlashShip50',
            'title' => 'Giảm 50k cho đơn hàng đầu tiên',
            'description' => 'Áp dụng cho đơn hàng từ 100k trở lên',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_amount' => 100000,
            'expires_at' => Carbon::now()->addMonths(1),
        ]);

        Promotion::create([
            'code' => 'SUPERAPP',
            'title' => 'Giảm 20% tổng đơn hàng',
            'description' => 'Giảm tối đa 30k cho mọi dịch vụ',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'min_order_amount' => 50000,
            'max_discount_amount' => 30000,
            'expires_at' => Carbon::now()->addMonths(1),
        ]);

        Promotion::create([
            'code' => 'FREEFlashShip',
            'title' => 'Ưu đãi phí vận chuyển',
            'description' => 'Giảm 15k phí vận chuyển',
            'discount_type' => 'fixed',
            'discount_value' => 15000,
            'min_order_amount' => 0,
            'expires_at' => Carbon::now()->addMonths(1),
        ]);
    }
}
