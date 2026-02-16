<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $col) {
            $col->id();
            $col->string('code')->unique();
            $col->string('title');
            $col->string('description')->nullable();
            $col->enum('discount_type', ['fixed', 'percent']);
            $col->decimal('discount_value', 12, 2);
            $col->decimal('min_order_amount', 12, 2)->default(0);
            $col->decimal('max_discount_amount', 12, 2)->nullable();
            $col->timestamp('expires_at')->nullable();
            $col->integer('usage_limit')->nullable();
            $col->integer('used_count')->default(0);
            $col->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
            $table->dropColumn(['promotion_id', 'discount_amount']);
        });
        Schema::dropIfExists('promotions');
    }
};
