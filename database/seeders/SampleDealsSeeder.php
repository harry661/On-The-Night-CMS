<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deal;
use App\Models\Venue;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SampleDealsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get venues and their moderators
        $venues = Venue::with('user')->get();
        
        foreach ($venues as $venue) {
            $this->command->info("Creating deals for: {$venue->name}");
            
            // Create 2-3 sample deals for each venue
            $deals = [
                [
                    'title' => '2-4-1 Cocktails',
                    'description' => 'Buy one cocktail, get one free! Perfect for sharing with friends. Available on all premium cocktails.',
                    'deal_type' => '2-4-1',
                    'start_date' => now(),
                    'end_date' => now()->addDays(7),
                ],
                [
                    'title' => 'Happy Hour Special',
                    'description' => '50% off all drinks during happy hour. Join us for the best deals in town!',
                    'deal_type' => '50% off',
                    'start_date' => now()->addDays(1),
                    'end_date' => now()->addDays(14),
                ],
                [
                    'title' => 'Free Entry Friday',
                    'description' => 'No cover charge this Friday! Come and enjoy our amazing atmosphere and music.',
                    'deal_type' => 'Free entry',
                    'start_date' => now()->addDays(2),
                    'end_date' => now()->addDays(3),
                ],
            ];
            
            foreach ($deals as $dealData) {
                $deal = Deal::create([
                    'title' => $dealData['title'],
                    'description' => $dealData['description'],
                    'deal_type' => $dealData['deal_type'],
                    'start_date' => $dealData['start_date'],
                    'end_date' => $dealData['end_date'],
                    'is_active' => true,
                    'featured' => $dealData['deal_type'] === '2-4-1', // Feature 2-4-1 deals
                    'venue_id' => $venue->id,
                    'user_id' => $venue->user_id,
                ]);
                
                // Create a placeholder image for the deal
                $imageContent = $this->createDealImage($deal->title, $deal->deal_type);
                $filename = strtolower(str_replace([' ', '-'], '_', $deal->title)) . '.jpg';
                
                // Store the image
                Storage::disk('public')->put("deals/{$filename}", $imageContent);
                
                // Add to media collection
                $deal->addMediaFromDisk("deals/{$filename}", 'public')
                      ->toMediaCollection('deal_images');
                      
                $this->command->info("Deal created: {$deal->title}");
            }
        }
        
        $this->command->info('Sample deals created successfully!');
    }
    
    private function createDealImage($title, $dealType): string
    {
        // Create a simple colored rectangle as placeholder
        $width = 800;
        $height = 600;
        
        // Different colors for different deal types
        $colors = [
            '2-4-1' => [34, 197, 94], // Green
            '50% off' => [59, 130, 246], // Blue
            'Free entry' => [168, 85, 247], // Purple
            'default' => [196, 30, 65], // Red
        ];
        
        $color = $colors[$dealType] ?? $colors['default'];
        
        // Create image
        $image = imagecreate($width, $height);
        $bgColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Add deal title
        $font = 5; // Built-in font
        $text = $title;
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 - 20;
        
        imagestring($image, $font, $x, $y, $text, $textColor);
        
        // Add deal type
        $dealTypeText = $dealType;
        $dealTypeWidth = imagefontwidth($font) * strlen($dealTypeText);
        $dealTypeX = ($width - $dealTypeWidth) / 2;
        $dealTypeY = $y + $textHeight + 10;
        
        imagestring($image, $font, $dealTypeX, $dealTypeY, $dealTypeText, $textColor);
        
        // Add "Special Offer" subtitle
        $subtitle = "Special Offer";
        $subtitleWidth = imagefontwidth($font) * strlen($subtitle);
        $subtitleX = ($width - $subtitleWidth) / 2;
        $subtitleY = $dealTypeY + $textHeight + 10;
        
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