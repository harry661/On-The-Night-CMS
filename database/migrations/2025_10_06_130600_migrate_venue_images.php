<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Venue;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing images from media library to image field
        $venues = Venue::all();
        foreach ($venues as $venue) {
            $media = $venue->getFirstMedia('venue_images');
            if ($media) {
                $relativePath = str_replace(storage_path('app/public/'), '', $media->getPath());
                $venue->update(['image' => $relativePath]);
                echo "Updated venue: " . $venue->name . " with image: " . $relativePath . "\n";
            }
        }
        echo "Image migration completed!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear image field
        Venue::query()->update(['image' => null]);
    }
};
