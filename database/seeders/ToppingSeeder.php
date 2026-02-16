<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOptionGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('product_option_groups')->truncate();
        DB::table('product_options')->truncate();
        Schema::enableForeignKeyConstraints();

        $products = Product::all();

        foreach ($products as $product) {
            // Group 1: Topping
            $toppingGroup = ProductOptionGroup::create([
                'product_id' => $product->id,
                'name' => 'Topping đi kèm',
                'is_required' => false,
                'max_selectable' => 5,
            ]);

            $toppingGroup->options()->createMany([
                ['name' => 'Thêm trứng', 'price' => 5000],
                ['name' => 'Thêm sườn', 'price' => 20000],
                ['name' => 'Thêm chả', 'price' => 10000],
                ['name' => 'Thêm lạp xưởng', 'price' => 10000],
            ]);

            // Group 2: Size
            $sizeGroup = ProductOptionGroup::create([
                'product_id' => $product->id,
                'name' => 'Chọn kích cỡ',
                'is_required' => true,
                'max_selectable' => 1,
            ]);

            $sizeGroup->options()->createMany([
                ['name' => 'Size vừa (M)', 'price' => 0],
                ['name' => 'Size lớn (L)', 'price' => 15000],
            ]);
        }
    }
}
