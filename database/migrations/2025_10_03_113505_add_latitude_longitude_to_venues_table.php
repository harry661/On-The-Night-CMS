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
        Schema::table('venues', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('location_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->index(['latitude', 'longitude']); // Index for geolocation queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};