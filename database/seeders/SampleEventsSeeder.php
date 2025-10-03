<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Venue;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SampleEventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get venues and their moderators
        $venues = Venue::with('user')->get();
        
        foreach ($venues as $venue) {
            $this->command->info("Creating events for: {$venue->name}");
            
            // Create 3-4 sample events for each venue
            $events = [
                [
                    'title' => 'Friday Night Live Music',
                    'description' => 'Join us for an amazing night of live music featuring local artists and bands. Great atmosphere, drinks, and entertainment.',
                    'event_type' => 'Live Music',
                    'start_date' => now()->addDays(3)->setTime(20, 0),
                    'end_date' => now()->addDays(3)->setTime(23, 0),
                    'ticket_price' => 15.00,
                [
                    'title' => 'Saturday DJ Night',
                    'description' => 'Dance the night away with our resident DJ playing the best hits and electronic music. VIP tables available.',
                    'event_type' => 'DJ Night',
                    'start_date' => now()->addDays(4)->setTime(21, 0),
                    'end_date' => now()->addDays(5)->setTime(2, 0),
                    'ticket_price' => 10.00,
                [
                    'title' => 'Comedy Show Night',
                    'description' => 'Laugh out loud with our featured comedians. A night of entertainment, drinks, and great food.',
                    'event_type' => 'Comedy Show',
                    'start_date' => now()->addDays(7)->setTime(19, 30),
                    'end_date' => now()->addDays(7)->setTime(22, 30),
                    'ticket_price' => 25.00,
                [
                    'title' => 'Private Corporate Event',
                    'description' => 'Exclusive corporate event with networking, presentations, and entertainment. Contact us for private bookings.',
                    'event_type' => 'Corporate Event',
                    'start_date' => now()->addDays(10)->setTime(18, 0),
                    'end_date' => now()->addDays(10)->setTime(22, 0),
                    'ticket_price' => null, // Private event
            ];
            
            foreach ($events as $eventData) {
                $event = Event::create([
                    'title' => $eventData['title'],
                    'description' => $eventData['description'],
                    'event_type' => $eventData['event_type'],
                    'start_date' => $eventData['start_date'],
                    'end_date' => $eventData['end_date'],
                    'ticket_price' => $eventData['ticket_price'],
                    'capacity' => $eventData['capacity'],
                    'is_active' => true,
                    'featured' => $eventData['event_type'] === 'Live Music', // Feature live music events
                    'sold_out' => false,
                    'venue_id' => $venue->id,
                    'user_id' => $venue->user_id,
                ]);
                
                // Create a placeholder image for the event
                $imageContent = $this->createEventImage($event->title, $event->event_type);
                $filename = strtolower(str_replace([' ', '-'], '_', $event->title)) . '.jpg';
                
                // Store the image
                Storage::disk('public')->put("events/{$filename}", $imageContent);
                
                // Add to media collection
                $event->addMediaFromDisk("events/{$filename}", 'public')
                      ->toMediaCollection('event_images');
                      
                $this->command->info("Event created: {$event->title}");
            }
        }
        
        $this->command->info('Sample events created successfully!');
    }
    
    private function createEventImage($title, $eventType): string
    {
        // Create a simple colored rectangle as placeholder
        $width = 800;
        $height = 600;
        
        // Different colors for different event types
        $colors = [
            'Live Music' => [34, 197, 94], // Green
            'DJ Night' => [168, 85, 247], // Purple
            'Comedy Show' => [251, 191, 36], // Yellow
            'Corporate Event' => [59, 130, 246], // Blue
            'Private Party' => [236, 72, 153], // Pink
            'Wedding' => [196, 30, 65], // Red
            'Birthday Party' => [245, 158, 11], // Orange
            'Other' => [107, 114, 128], // Gray
        ];
        
        $color = $colors[$eventType] ?? $colors['Other'];
        
        // Create image
        $image = imagecreate($width, $height);
        $bgColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Add event title
        $font = 5; // Built-in font
        $text = $title;
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 - 20;
        
        imagestring($image, $font, $x, $y, $text, $textColor);
        
        // Add event type
        $eventTypeText = $eventType;
        $eventTypeWidth = imagefontwidth($font) * strlen($eventTypeText);
        $eventTypeX = ($width - $eventTypeWidth) / 2;
        $eventTypeY = $y + $textHeight + 10;
        
        imagestring($image, $font, $eventTypeX, $eventTypeY, $eventTypeText, $textColor);
        
        // Add "Event" subtitle
        $subtitle = "Event";
        $subtitleWidth = imagefontwidth($font) * strlen($subtitle);
        $subtitleX = ($width - $subtitleWidth) / 2;
        $subtitleY = $eventTypeY + $textHeight + 10;
        
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