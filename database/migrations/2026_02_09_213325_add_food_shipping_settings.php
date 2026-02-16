<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')->insert([
            [
                'key' => 'food_base_price',
                'value' => '13000',
                'group' => 'food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'food_additional_km_price',
                'value' => '4000',
                'group' => 'food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'food_base_distance',
                'value' => '3',
                'group' => 'food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('group', 'food')->delete();
    }
};
