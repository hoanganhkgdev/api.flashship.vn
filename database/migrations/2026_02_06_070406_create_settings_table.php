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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Insert default bike pricing settings
        DB::table('settings')->insert([
            [
                'key' => 'bike_base_price', // for 1-3km
                'value' => '13000',
                'group' => 'bike',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bike_additional_km_price', // after 3km
                'value' => '5000',
                'group' => 'bike',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bike_base_distance', // km
                'value' => '3',
                'group' => 'bike',
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
        Schema::dropIfExists('settings');
    }
};
