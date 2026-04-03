<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('image_groups', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('caption');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('location_name')->nullable()->after('longitude');
            $table->string('location_address')->nullable()->after('location_name');
            $table->boolean('show_on_map')->default(true)->after('location_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('image_groups', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'location_name', 'location_address', 'show_on_map']);
        });
    }
};
