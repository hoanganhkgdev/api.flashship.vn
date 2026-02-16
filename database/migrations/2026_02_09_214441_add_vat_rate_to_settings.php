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
                'key' => 'food_vat_rate',
                'value' => '10', // 10%
                'group' => 'food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        if (!Schema::hasColumn('orders', 'vat_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('vat_amount', 15, 2)->default(0)->after('discount_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'food_vat_rate')->delete();

        if (Schema::hasColumn('orders', 'vat_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('vat_amount');
            });
        }
    }
};
