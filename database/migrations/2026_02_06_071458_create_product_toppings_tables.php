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
        Schema::create('product_option_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Topping", "Chọn Size"
            $table->boolean('is_required')->default(false);
            $table->integer('max_selectable')->default(1);
            $table->timestamps();
        });

        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_group_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Add options column to order_items to store selected toppings
        Schema::table('order_items', function (Blueprint $table) {
            $table->json('options')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('options');
        });
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_option_groups');
    }
};
