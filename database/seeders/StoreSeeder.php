<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or create a merchant user
        $merchant = User::updateOrCreate(
            ['email' => 'merchant@flashship.vn'],
            [
                'name' => 'Demo Merchant',
                'password' => 'password',
                'role' => 'merchant',
                'is_active' => true,
            ]
        );

        // 2. Get the "Đồ ăn" (Food) service
        $foodService = Service::where('slug', 'do-an')->first();
        if (!$foodService) {
            $foodService = Service::create([
                'name' => 'Đồ ăn',
                'slug' => 'do-an',
                'icon' => 'fast-food',
                'is_active' => true,
            ]);
        }

        // 3. Define 10 stores in Rach Gia
        $stores = [
            [
                'name' => 'Bún Cá Rạch Giá - Mạc Cửu',
                'address' => '50 Mạc Cửu, Vĩnh Thanh, Rạch Giá, Kiên Giang',
                'lat' => 10.012345,
                'lng' => 105.087654,
            ],
            [
                'name' => 'Cơm Tấm Demo - Nguyễn Trung Trực',
                'address' => '123 Nguyễn Trung Trực, Vĩnh Bảo, Rạch Giá, Kiên Giang',
                'lat' => 10.005432,
                'lng' => 105.101234,
            ],
            [
                'name' => 'Phở Bắc Hải - Phan Thị Ràng',
                'address' => 'Khu đô thị Phú Cường, Phan Thị Ràng, Rạch Giá, Kiên Giang',
                'lat' => 9.987654,
                'lng' => 105.112345,
            ],
            [
                'name' => 'Bánh Mì Thịt Nướng - Trần Phú',
                'address' => '88 Trần Phú, Vĩnh Thanh Vân, Rạch Giá, Kiên Giang',
                'lat' => 10.008765,
                'lng' => 105.093456,
            ],
            [
                'name' => 'Hủ Tiếu Nam Vang - Lâm Quang Ky',
                'address' => '200 Lâm Quang Ky, Vĩnh Lạc, Rạch Giá, Kiên Giang',
                'lat' => 9.994321,
                'lng' => 105.105678,
            ],
            [
                'name' => 'Trà Sữa Demo - Tôn Đức Thắng',
                'address' => '15 Tôn Đức Thắng, Vĩnh Bảo, Rạch Giá, Kiên Giang',
                'lat' => 10.003210,
                'lng' => 105.098765,
            ],
            [
                'name' => 'Ốc Đêm Rạch Giá - 3/2',
                'address' => 'Lô A11 đường 3/2, Rạch Giá, Kiên Giang',
                'lat' => 9.981234,
                'lng' => 105.109876,
            ],
            [
                'name' => 'Pizza Flash - Mai Thị Hồng Hạnh',
                'address' => 'Mai Thị Hồng Hạnh, Rạch Sỏi, Rạch Giá, Kiên Giang',
                'lat' => 9.954321,
                'lng' => 105.123456,
            ],
            [
                'name' => 'Lẩu Mắm Demo - Cô Bắc',
                'address' => '45 Cô Bắc, Vĩnh Bảo, Rạch Giá, Kiên Giang',
                'lat' => 10.001234,
                'lng' => 105.094567,
            ],
            [
                'name' => 'Cafe Sáng - Phan Huy Chú',
                'address' => 'Phan Huy Chú, Vĩnh Hiệp, Rạch Giá, Kiên Giang',
                'lat' => 10.023456,
                'lng' => 105.115678,
            ],
        ];

        foreach ($stores as $storeData) {
            Store::updateOrCreate(
                ['name' => $storeData['name'], 'lat' => $storeData['lat']],
                array_merge($storeData, [
                    'user_id' => $merchant->id,
                    'service_id' => $foodService->id,
                    'image' => 'https://picsum.photos/seed/' . md5($storeData['name']) . '/400/300',
                    'is_active' => true,
                    'rating' => rand(40, 50) / 10,
                ])
            );
        }
    }
}
