<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Add initial services
        \App\Models\Service::create([
            'name' => 'Giao đồ ăn',
            'slug' => 'food',
            'icon' => 'fastfood',
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Xe ôm',
            'slug' => 'bike',
            'icon' => 'motorcycle',
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Giao hàng',
            'slug' => 'delivery',
            'icon' => 'local_shipping',
            'is_active' => true,
        ]);

        // Create accounts for testing
        \App\Models\User::create([
            'name' => 'Admin FlashShip',
            'email' => 'admin@flashship.vn',
            'phone' => '0987654321',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'Tài Xế FlashShip',
            'email' => 'driver@flashship.vn',
            'phone' => '0909123456',
            'password' => bcrypt('password'),
            'role' => 'driver',
        ]);

        \App\Models\User::create([
            'name' => 'Khách Hàng FlashShip',
            'email' => 'user@flashship.vn',
            'phone' => '0909888999',
            'password' => bcrypt('password'),
            'role' => 'user',
            'balance' => 500000,
        ]);

        $this->call([
            StoreSeeder::class,
            ToppingSeeder::class,
            MartSeeder::class,
            PromotionSeeder::class,
        ]);
    }
}
