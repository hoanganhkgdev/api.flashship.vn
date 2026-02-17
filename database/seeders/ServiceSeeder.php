<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Xe máy',
                'slug' => 'xe-may',
                'icon' => 'motorcycle',
                'is_active' => true,
            ],
            [
                'name' => 'Xe ô tô',
                'slug' => 'xe-o-to',
                'icon' => 'car',
                'is_active' => true,
            ],
            [
                'name' => 'Đồ ăn',
                'slug' => 'do-an',
                'icon' => 'fast-food',
                'is_active' => true,
            ],
            [
                'name' => 'Giao hàng',
                'slug' => 'giao-hang',
                'icon' => 'cube',
                'is_active' => true,
            ],
            [
                'name' => 'Tạp hóa',
                'slug' => 'tap-hoa',
                'icon' => 'cart',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}
