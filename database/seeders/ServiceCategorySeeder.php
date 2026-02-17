<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get the "Đồ ăn" (Food) service
        $foodService = Service::where('slug', 'do-an')->first();

        if (!$foodService) {
            return;
        }

        // 2. Define categories for "Đồ ăn"
        $categories = [
            [
                'name' => 'Cơm trưa',
                'icon' => 'restaurant',
                'image' => 'https://picsum.photos/seed/cat-rice/400/300',
            ],
            [
                'name' => 'Bún & Phở',
                'icon' => 'bowl-food',
                'image' => 'https://picsum.photos/seed/cat-noodles/400/300',
            ],
            [
                'name' => 'Trà sữa & Cafe',
                'icon' => 'cafe',
                'image' => 'https://picsum.photos/seed/cat-drink/400/300',
            ],
            [
                'name' => 'Ăn vặt',
                'icon' => 'fast-food',
                'image' => 'https://picsum.photos/seed/cat-snack/400/300',
            ],
            [
                'name' => 'Bánh mì',
                'icon' => 'pizza',
                'image' => 'https://picsum.photos/seed/cat-bread/400/300',
            ],
            [
                'name' => 'Hải sản',
                'icon' => 'fish',
                'image' => 'https://picsum.photos/seed/cat-seafood/400/300',
            ],
            [
                'name' => 'Lẩu & Nướng',
                'icon' => 'flame',
                'image' => 'https://picsum.photos/seed/cat-bbq/400/300',
            ],
            [
                'name' => 'Tráng miệng',
                'icon' => 'ice-cream',
                'image' => 'https://picsum.photos/seed/cat-dessert/400/300',
            ],
            [
                'name' => 'Chay',
                'icon' => 'leaf',
                'image' => 'https://picsum.photos/seed/cat-vegan/400/300',
            ],
            [
                'name' => 'Pizza & Burger',
                'icon' => 'pizza',
                'image' => 'https://picsum.photos/seed/cat-fastfood/400/300',
            ],
        ];

        foreach ($categories as $index => $cat) {
            ServiceCategory::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'service_id' => $foodService->id,
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'image' => $cat['image'],
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
