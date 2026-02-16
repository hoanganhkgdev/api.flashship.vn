<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            $table->decimal('rating', 3, 1)->default(5.0)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
            $table->dropColumn('rating');
        });
    }
};
