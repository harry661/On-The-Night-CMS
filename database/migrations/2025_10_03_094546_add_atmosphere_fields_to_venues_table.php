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
            $table->string('music_genres')->nullable()->after('amenities');
            $table->string('drink_types')->nullable()->after('music_genres');
            $table->string('crowd_type')->nullable()->after('drink_types');
            $table->string('dress_code')->nullable()->after('crowd_type');
            $table->text('atmosphere_description')->nullable()->after('dress_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn([
                'music_genres',
                'drink_types',
                'crowd_type',
                'dress_code',
                'atmosphere_description'
            ]);
        });
    }
};
