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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->text('review_text');
            $table->integer('rating'); // 1-5 stars
            $table->string('reviewer_name')->nullable(); // App user's display name
            $table->string('reviewer_email')->nullable(); // App user's email
            $table->boolean('is_featured')->default(false); // Admin can feature reviews
            $table->boolean('is_approved')->default(false); // Admin moderation
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // App user who left review
            $table->timestamps();
            
            // Prevent duplicate reviews from same user for same venue
            $table->unique(['venue_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
