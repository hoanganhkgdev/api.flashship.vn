<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RachGiaFoodStoreSeeder extends Seeder
{
    public function run(): void
    {
        // Get the food service (do-an)
        $foodService = Service::where('slug', 'do-an')->first();

        if (!$foodService) {
            $this->command->error('Food service not found! Please run DatabaseSeeder first.');
            return;
        }

        // Create Global Categories
        $categoriesData = [
            ['name' => 'Cơm', 'slug' => 'com', 'sort_order' => 1],
            ['name' => 'Bún/Phở', 'slug' => 'bun-pho', 'sort_order' => 2],
            ['name' => 'Trà sữa', 'slug' => 'tra-sua', 'sort_order' => 3],
            ['name' => 'Ăn vặt', 'slug' => 'an-vat', 'sort_order' => 4],
            ['name' => 'Gà rán', 'slug' => 'ga-ran', 'sort_order' => 5],
            ['name' => 'Lẩu', 'slug' => 'lau', 'sort_order' => 6],
            ['name' => 'Pizza', 'slug' => 'pizza', 'sort_order' => 7],
            ['name' => 'Hủ tiếu', 'slug' => 'hu-tieu', 'sort_order' => 8],
        ];

        $globalCategories = [];
        foreach ($categoriesData as $cat) {
            $globalCategories[$cat['slug']] = ServiceCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'service_id' => $foodService->id,
                    'name' => $cat['name'],
                    'sort_order' => $cat['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        // Get or create a merchant user
        $merchant = User::firstOrCreate(
            ['email' => 'merchant@flashship.vn'],
            [
                'name' => 'Merchant FlashShip',
                'phone' => '0909111222',
                'password' => bcrypt('password'),
                'role' => 'merchant',
            ]
        );

        $stores = [
            [
                'name' => 'Quán Cơm Tấm Sườn Bì Chả',
                'address' => '123 Nguyễn Trung Trực, TP. Rạch Giá',
                'phone' => '0297 3871 234',
                'image' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=Com+Tam&size=200&background=FF5722&color=fff',
                'products' => [
                    ['name' => 'Cơm Tấm Sườn Đặc Biệt', 'cat' => 'com', 'price' => 45000],
                    ['name' => 'Cơm Tấm Sườn Bì Chả', 'cat' => 'com', 'price' => 55000],
                ]
            ],
            [
                'name' => 'Bánh Mì Xíu Mại Rạch Giá',
                'address' => '45 Trần Hưng Đạo, TP. Rạch Giá',
                'phone' => '0297 3862 456',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=Banh+Mi&size=200&background=FF9800&color=fff',
                'products' => [
                    ['name' => 'Bánh Mì Xíu Mại Trứng', 'cat' => 'an-vat', 'price' => 25000],
                    ['name' => 'Bánh Mì Heo Quay', 'cat' => 'an-vat', 'price' => 30000],
                ]
            ],
            [
                'name' => 'Phở Bò Hà Nội',
                'address' => '78 Lê Lợi, TP. Rạch Giá',
                'phone' => '0297 3853 789',
                'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=Pho+Bo&size=200&background=4CAF50&color=fff',
                'products' => [
                    ['name' => 'Phở Bò Tái Nạm', 'cat' => 'bun-pho', 'price' => 50000],
                    ['name' => 'Phở Bò Viên', 'cat' => 'bun-pho', 'price' => 45000],
                ]
            ],
            [
                'name' => 'Trà Sữa Gong Cha',
                'address' => '167 Lý Tự Trọng, TP. Rạch Giá',
                'phone' => '0297 3808 789',
                'image' => 'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=Tra+Sua&size=200&background=795548&color=fff',
                'products' => [
                    ['name' => 'Trà Sữa Trân Châu Đen', 'cat' => 'tra-sua', 'price' => 45000],
                    ['name' => 'Trà Đào Cam Sả', 'cat' => 'tra-sua', 'price' => 35000],
                ]
            ],
            [
                'name' => 'KFC Rạch Giá',
                'address' => 'Phan Thị Ràng, KĐT Phú Cường, TP. Rạch Giá',
                'phone' => '1900 6886',
                'image' => 'https://images.unsplash.com/photo-1513639733135-3147bac18cb0?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=KFC&size=200&background=E91E63&color=fff',
                'products' => [
                    ['name' => 'Gà Rán Truyền Thống', 'cat' => 'ga-ran', 'price' => 85000],
                    ['name' => 'Burger Tôm', 'cat' => 'ga-ran', 'price' => 45000],
                ]
            ],
            [
                'name' => 'The Pizza Company',
                'address' => 'Trung Tâm Thương Mại Vincom Rạch Giá',
                'phone' => '1900 6336',
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=Pizza&size=200&background=4CAF50&color=fff',
                'products' => [
                    ['name' => 'Pizza Hải Sản Đào', 'cat' => 'pizza', 'price' => 189000],
                    ['name' => 'Mì Ý Sốt Bò Bằm', 'cat' => 'pizza', 'price' => 99000],
                ]
            ],
            [
                'name' => 'Lẩu Phan Rạch Giá',
                'address' => 'B2-20 Lô B2, TP. Rạch Giá',
                'phone' => '1900 2808',
                'image' => 'https://images.unsplash.com/photo-1547586686-24ba0262189c?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=Lau+Phan&size=200&background=F44336&color=fff',
                'products' => [
                    ['name' => 'Set Lẩu Buffet 199k', 'cat' => 'lau', 'price' => 199000],
                    ['name' => 'Bò Mỹ Thêm', 'cat' => 'lau', 'price' => 59000],
                ]
            ],
            [
                'name' => 'Hủ Tiếu Nam Vang 555',
                'address' => '321 Mai Thị Hồng Hạnh, TP. Rạch Giá',
                'phone' => '0297 3888 555',
                'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=Hu+Tieu&size=200&background=FFC107&color=fff',
                'products' => [
                    ['name' => 'Hủ Tiếu Nam Vang Đặc Biệt', 'cat' => 'hu-tieu', 'price' => 55000],
                    ['name' => 'Hủ Tiếu Khô Nam Vang', 'cat' => 'hu-tieu', 'price' => 55000],
                ]
            ],
            [
                'name' => 'Bún Bò Huế Chú Bảy',
                'address' => '12 Nguyễn Bỉnh Khiêm, TP. Rạch Giá',
                'phone' => '0297 3899 999',
                'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=Bun+Bo&size=200&background=9C27B0&color=fff',
                'products' => [
                    ['name' => 'Bún Bò Huế Giò Chả', 'cat' => 'bun-pho', 'price' => 45000],
                    ['name' => 'Bún Bò Huế Đặc Biệt', 'cat' => 'bun-pho', 'price' => 60000],
                ]
            ],
            [
                'name' => 'Ăn Vặt Bé Bi',
                'address' => '89 Trần Quang Khải, TP. Rạch Giá',
                'phone' => '0912 345 678',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800',
                'logo' => 'https://ui-avatars.com/api/?name=An+Vat&size=200&background=03A9F4&color=fff',
                'products' => [
                    ['name' => 'Trà Dâu Tầm', 'cat' => 'an-vat', 'price' => 25000],
                    ['name' => 'Bánh Tráng Trộn', 'cat' => 'an-vat', 'price' => 20000],
                    ['name' => 'Xoài Lắc Muối Tôm', 'cat' => 'an-vat', 'price' => 15000],
                ]
            ],
        ];

        foreach ($stores as $storeData) {
            $store = Store::updateOrCreate(
                ['name' => $storeData['name']],
                [
                    'user_id' => $merchant->id,
                    'service_id' => $foodService->id,
                    'address' => $storeData['address'],
                    'phone' => $storeData['phone'],
                    'lat' => 10.0124 + (rand(-100, 100) / 10000),
                    'lng' => 105.0808 + (rand(-100, 100) / 10000),
                    'image' => $storeData['image'],
                    'logo' => $storeData['logo'],
                    'is_active' => true,
                    'rating' => rand(40, 50) / 10,
                ]
            );

            foreach ($storeData['products'] as $pData) {
                $product = Product::updateOrCreate(
                    ['name' => $pData['name'], 'store_id' => $store->id],
                    [
                        'service_category_id' => $globalCategories[$pData['cat']]->id,
                        'description' => 'Món ngon mỗi ngày tại ' . $store->name,
                        'price' => $pData['price'],
                        'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500',
                        'is_available' => true,
                    ]
                );

                // Add Toppings based on Category
                $this->seedToppings($product, $pData['cat']);
            }
        }

        $this->command->info('✅ Created global categories, stores and products with TOPPINGS successfully!');
    }

    private function seedToppings($product, $catSlug)
    {
        $toppings = [
            'com' => [
                'Chọn thêm' => [
                    ['name' => 'Thêm Sườn', 'price' => 15000],
                    ['name' => 'Thêm Trứng Ốp La', 'price' => 5000],
                    ['name' => 'Thêm Chả Hấp', 'price' => 7000],
                    ['name' => 'Thêm Cơm trắng', 'price' => 5000],
                ]
            ],
            'bun-pho' => [
                'Chọn thêm' => [
                    ['name' => 'Thêm Thịt/Bò', 'price' => 15000],
                    ['name' => 'Thêm Gân/Vè', 'price' => 10000],
                    ['name' => 'Thêm Bò Viên', 'price' => 10000],
                    ['name' => 'Thêm Bánh Phở/Bún', 'price' => 5000],
                    ['name' => 'Trứng Chần', 'price' => 5000],
                ]
            ],
            'tra-sua' => [
                'Chọn Size' => [
                    ['name' => 'Size M', 'price' => 0],
                    ['name' => 'Size L', 'price' => 8000],
                ],
                'Topping' => [
                    ['name' => 'Trân Châu Đen', 'price' => 5000],
                    ['name' => 'Thạch Trái Cây', 'price' => 5000],
                    ['name' => 'Kem Cheese', 'price' => 10000],
                    ['name' => 'Pudding Trứng', 'price' => 7000],
                ]
            ],
            'an-vat' => [
                'Thêm gia vị' => [
                    ['name' => 'Thêm Tương Ớt', 'price' => 0],
                    ['name' => 'Thêm Sốt Mayonnaise', 'price' => 2000],
                    ['name' => 'Lắc Phô Mai', 'price' => 5000],
                ]
            ],
            'ga-ran' => [
                'Lựa chọn' => [
                    ['name' => 'Cay', 'price' => 0],
                    ['name' => 'Không cay', 'price' => 0],
                ],
                'Thêm sốt' => [
                    ['name' => 'Sốt Phô Mai', 'price' => 7000],
                    ['name' => 'Sốt BBQ', 'price' => 5000],
                ]
            ],
            'pizza' => [
                'Kích thước' => [
                    ['name' => 'Dòng Đế Mỏng 9 inch', 'price' => 0],
                    ['name' => 'Dòng Đế Dày 9 inch', 'price' => 0],
                    ['name' => 'Dòng Đế Mỏng 12 inch', 'price' => 80000],
                ],
                'Thêm phô mai' => [
                    ['name' => 'Gấp đôi Phô mai', 'price' => 25000],
                    ['name' => 'Viền Phô mai', 'price' => 35000],
                ]
            ],
            'hu-tieu' => [
                'Chọn thêm' => [
                    ['name' => 'Thêm Tôm', 'price' => 10000],
                    ['name' => 'Thêm Xá Xíu', 'price' => 10000],
                    ['name' => 'Thêm Xương Ống', 'price' => 15000],
                    ['name' => 'Thêm Hủ Tiếu', 'price' => 5000],
                ]
            ],
            'lau' => [
                'Món nhúng thêm' => [
                    ['name' => 'Bắp Bò Mỹ', 'price' => 55000],
                    ['name' => 'Nấm Kim Châm', 'price' => 20000],
                    ['name' => 'Rau Tổng Hợp', 'price' => 25000],
                    ['name' => 'Đậu Phụ', 'price' => 10000],
                ]
            ],
        ];

        if (!isset($toppings[$catSlug]))
            return;

        foreach ($toppings[$catSlug] as $groupName => $options) {
            $group = \App\Models\ProductOptionGroup::updateOrCreate(
                ['product_id' => $product->id, 'name' => $groupName],
                [
                    'is_required' => str_contains($groupName, 'Size') || str_contains($groupName, 'Kích thước') || str_contains($groupName, 'Lựa chọn'),
                    'max_selectable' => str_contains($groupName, 'Size') || str_contains($groupName, 'Kích thước') || str_contains($groupName, 'Lựa chọn') ? 1 : 5,
                ]
            );

            foreach ($options as $opt) {
                \App\Models\ProductOption::updateOrCreate(
                    ['product_option_group_id' => $group->id, 'name' => $opt['name']],
                    ['price' => $opt['price'], 'is_available' => true]
                );
            }
        }
    }
}
