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
        User::updateOrCreate(
            ['email' => 'admin@flashship.vn'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->call([
            ServiceSeeder::class,
            ServiceCategorySeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
            PromotionSeeder::class,
        ]);
    }
}
