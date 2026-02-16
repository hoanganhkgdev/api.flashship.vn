<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Store;
use App\Models\ServiceCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MartSeeder extends Seeder
{
    public function run(): void
    {
        $merchant = User::where('role', 'merchant')->first();
        if (!$merchant) {
            $merchant = User::create([
                'name' => 'Merchant Mart',
                'email' => 'merchant.mart@flashship.vn',
                'phone' => '0123456780',
                'password' => bcrypt('password'),
                'role' => 'merchant',
            ]);
        }

        // 1. Create Mart Service
        $service = Service::updateOrCreate(
            ['slug' => 'mart'],
            [
                'name' => 'FlashShip Mart',
                'icon' => 'shopping_cart'
            ]
        );

        // 2. Create Global Categories for Mart
        $cat1 = ServiceCategory::updateOrCreate(
            ['slug' => 'rau-cu-tuoi', 'service_id' => $service->id],
            ['name' => 'Rau củ tươi', 'is_active' => true]
        );
        $cat2 = ServiceCategory::updateOrCreate(
            ['slug' => 'thit-hai-san', 'service_id' => $service->id],
            ['name' => 'Thịt & Hải sản', 'is_active' => true]
        );

        // 3. Create a Mart Store
        $mart = Store::create([
            'user_id' => $merchant->id,
            'service_id' => $service->id,
            'name' => 'Siêu thị FlashShip Mart - Quận 1',
            'address' => '456 Lê Lợi, Bến Thành, Quận 1',
            'lat' => 10.771622,
            'lng' => 106.669172,
            'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?q=80&w=2000&auto=format&fit=crop',
            'rating' => 4.8,
            'is_active' => true,
        ]);

        // 4. Create Products
        Product::create([
            'store_id' => $mart->id,
            'service_category_id' => $cat1->id,
            'name' => 'Xà lách thủy canh',
            'description' => 'Tươi ngon, sạch 100%',
            'price' => 25000,
            'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800',
            'is_available' => true,
        ]);

        // Another Mart Store
        $mart2 = Store::create([
            'user_id' => $merchant->id,
            'service_id' => $service->id,
            'name' => 'Cửa hàng tiện lợi FlashShip 24h',
            'address' => '789 Cách Mạng Tháng 8, Quận 10',
            'lat' => 10.781622,
            'lng' => 106.661172,
            'image' => 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?q=80&w=2000&auto=format&fit=crop',
            'rating' => 4.5,
            'is_active' => true,
        ]);
    }
}
