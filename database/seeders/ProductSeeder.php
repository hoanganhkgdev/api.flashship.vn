<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionGroup;
use App\Models\ServiceCategory;
use App\Models\Store;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all();
        $categories = ServiceCategory::all();

        if ($stores->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $productData = [
            'Cơm trưa' => [
                ['name' => 'Cơm sườn bì chả', 'price' => 35000],
                ['name' => 'Cơm gà xối mỡ', 'price' => 40000],
                ['name' => 'Cơm thịt kho trứng', 'price' => 30000],
                ['name' => 'Cơm cá kho tộ', 'price' => 35000],
                ['name' => 'Cơm xá xíu', 'price' => 35000],
            ],
            'Bún & Phở' => [
                ['name' => 'Phở bò đặc biệt', 'price' => 50000],
                ['name' => 'Bún cá Rạch Giá', 'price' => 35000],
                ['name' => 'Bún riêu cua', 'price' => 30000],
                ['name' => 'Hủ tiếu Nam Vang', 'price' => 45000],
                ['name' => 'Bún mắm Kiên Giang', 'price' => 45000],
            ],
            'Trà sữa & Cafe' => [
                ['name' => 'Trà sữa truyền thống', 'price' => 25000],
                ['name' => 'Cafe sữa đá', 'price' => 15000],
                ['name' => 'Trà đào cam sả', 'price' => 30000],
                ['name' => 'Sữa tươi trân châu đường đen', 'price' => 35000],
                ['name' => 'Matcha Latte', 'price' => 35000],
            ],
            'Ăn vặt' => [
                ['name' => 'Bánh tráng trộn', 'price' => 20000],
                ['name' => 'Cá viên chiên thập cẩm', 'price' => 30000],
                ['name' => 'Khoai tây chiên', 'price' => 25000],
                ['name' => 'Xoài lắc', 'price' => 20000],
                ['name' => 'Phô mai que', 'price' => 25000],
            ],
            'Bánh mì' => [
                ['name' => 'Bánh mì thịt nguội', 'price' => 20000],
                ['name' => 'Bánh mì heo quay', 'price' => 25000],
                ['name' => 'Bánh mì ốp la', 'price' => 15000],
                ['name' => 'Bánh mì chả cá', 'price' => 20000],
                ['name' => 'Bánh mì gà xé', 'price' => 20000],
            ],
        ];

        foreach ($stores as $store) {
            // Pick a random category for each store to make it consistent (e.g. a noodle shop has noodles)
            $storeCategory = $categories->random();

            // Get products for this category or default to random ones
            $items = $productData[$storeCategory->name] ?? $productData['Cơm trưa'];

            foreach ($items as $item) {
                $product = Product::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'name' => $item['name']
                    ],
                    [
                        'service_category_id' => $storeCategory->id,
                        'description' => 'Món ăn thơm ngon, nóng hổi được chế biến từ nguyên liệu tươi sạch mỗi ngày tại ' . $store->name,
                        'price' => $item['price'],
                        'image' => 'https://picsum.photos/seed/' . md5($item['name'] . $store->id) . '/500/400',
                        'is_available' => true,
                    ]
                );

                // Add Option Groups
                if (str_contains($item['name'], 'Trà sữa') || str_contains($item['name'], 'Cafe') || str_contains($item['name'], 'Latte')) {
                    $this->addDrinkOptions($product);
                } else if (str_contains($item['name'], 'Cơm') || str_contains($item['name'], 'Phở') || str_contains($item['name'], 'Bún')) {
                    $this->addFoodOptions($product);
                } else {
                    $this->addGeneralOptions($product);
                }
            }
        }
    }

    private function addDrinkOptions($product)
    {
        // Size group
        $sizeGroup = ProductOptionGroup::updateOrCreate(
            ['product_id' => $product->id, 'name' => 'Chọn Size'],
            ['is_required' => true, 'max_selectable' => 1]
        );

        ProductOption::updateOrCreate(['product_option_group_id' => $sizeGroup->id, 'name' => 'Size M'], ['price' => 0]);
        ProductOption::updateOrCreate(['product_option_group_id' => $sizeGroup->id, 'name' => 'Size L'], ['price' => 5000]);

        // Topping group
        $toppingGroup = ProductOptionGroup::updateOrCreate(
            ['product_id' => $product->id, 'name' => 'Thêm Topping'],
            ['is_required' => false, 'max_selectable' => 3]
        );

        ProductOption::updateOrCreate(['product_option_group_id' => $toppingGroup->id, 'name' => 'Trân châu trắng'], ['price' => 5000]);
        ProductOption::updateOrCreate(['product_option_group_id' => $toppingGroup->id, 'name' => 'Thạch trái cây'], ['price' => 5000]);
        ProductOption::updateOrCreate(['product_option_group_id' => $toppingGroup->id, 'name' => 'Kem Cheese'], ['price' => 10000]);
    }

    private function addFoodOptions($product)
    {
        $extraGroup = ProductOptionGroup::updateOrCreate(
            ['product_id' => $product->id, 'name' => 'Món thêm'],
            ['is_required' => false, 'max_selectable' => 5]
        );

        ProductOption::updateOrCreate(['product_option_group_id' => $extraGroup->id, 'name' => 'Thêm trứng ốp la'], ['price' => 5000]);
        ProductOption::updateOrCreate(['product_option_group_id' => $extraGroup->id, 'name' => 'Thêm thịt'], ['price' => 10000]);
        ProductOption::updateOrCreate(['product_option_group_id' => $extraGroup->id, 'name' => 'Thêm cơm/bún'], ['price' => 5000]);
    }

    private function addGeneralOptions($product)
    {
        $noteGroup = ProductOptionGroup::updateOrCreate(
            ['product_id' => $product->id, 'name' => 'Tùy chọn'],
            ['is_required' => false, 'max_selectable' => 1]
        );

        ProductOption::updateOrCreate(['product_option_group_id' => $noteGroup->id, 'name' => 'Cay nhiều'], ['price' => 0]);
        ProductOption::updateOrCreate(['product_option_group_id' => $noteGroup->id, 'name' => 'Không cay'], ['price' => 0]);
    }
}
