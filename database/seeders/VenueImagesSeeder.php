<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venue;
use Illuminate\Support\Facades\Storage;

class VenueImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create placeholder images for each venue
        $venues = Venue::all();
        
        foreach ($venues as $venue) {
            $this->command->info("Adding image for: {$venue->name}");
            
            // Create a simple placeholder image based on venue type
            $imageContent = $this->createPlaceholderImage($venue->name);
            $filename = strtolower(str_replace(' ', '_', $venue->name)) . '.jpg';
            
            // Store the image
            Storage::disk('public')->put("venues/{$filename}", $imageContent);
            
            // Add to media collection
            $venue->addMediaFromDisk("venues/{$filename}", 'public')
                  ->toMediaCollection('venue_images');
                  
            $this->command->info("Image added for: {$venue->name}");
        }
    }
    
    private function createPlaceholderImage($venueName): string
    {
        // Create a simple colored rectangle as placeholder
        $width = 800;
        $height = 600;
        
        // Different colors for different venue types
        $colors = [
            'Electrik Warehouse' => [196, 30, 65], // Red
            'Level' => [30, 144, 255], // Blue
            'Boom Battle Bar' => [255, 165, 0], // Orange
            'The Night Club' => [128, 0, 128], // Purple
        ];
        
        $color = $colors[$venueName] ?? [100, 100, 100]; // Default gray
        
        // Create image
        $image = imagecreate($width, $height);
        $bgColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Add venue name
        $font = 5; // Built-in font
        $text = $venueName;
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        
        imagestring($image, $font, $x, $y, $text, $textColor);
        
        // Add "Venue Image" subtitle
        $subtitle = "Venue Image";
        $subtitleWidth = imagefontwidth($font) * strlen($subtitle);
        $subtitleX = ($width - $subtitleWidth) / 2;
        $subtitleY = $y + $textHeight + 10;
        
        imagestring($image, $font, $subtitleX, $subtitleY, $subtitle, $textColor);
        
        // Output as JPEG
        ob_start();
        imagejpeg($image, null, 90);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        imagedestroy($image);
        
        return $imageData;
    }
}