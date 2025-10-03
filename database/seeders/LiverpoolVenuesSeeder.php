<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Venue;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class LiverpoolVenuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the venue_moderator role
        $venueModeratorRole = Role::where('name', 'venue_moderator')->first();

        // Create venue moderators for each venue
        $electrikModerator = User::create([
            'name' => 'Electrik Warehouse Manager',
            'email' => 'manager@electrikwarehouse.com',
            'password' => Hash::make('password'),
        ]);
        $electrikModerator->assignRole($venueModeratorRole);

        $levelModerator = User::create([
            'name' => 'Level Manager',
            'email' => 'manager@level-liverpool.com',
            'password' => Hash::make('password'),
        ]);
        $levelModerator->assignRole($venueModeratorRole);

        $boomModerator = User::create([
            'name' => 'Boom Battle Bar Manager',
            'email' => 'manager@boombattlebar.com',
            'password' => Hash::make('password'),
        ]);
        $boomModerator->assignRole($venueModeratorRole);

        // Create Electrik Warehouse venue
        $electrikWarehouse = Venue::create([
            'name' => 'Electrik Warehouse',
            'description' => 'Liverpool\'s premier electronic music venue and nightclub. Featuring state-of-the-art sound systems, immersive lighting, and world-class DJs. Known for hosting the biggest names in electronic dance music.',
            'address' => 'Wood Street',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 4DQ',
            'country' => 'UK',
            'phone' => '+44 151 709 5000',
            'email' => 'info@electrikwarehouse.com',
            'website' => 'https://electrikwarehouse.com',
            'latitude' => 53.4084,
            'longitude' => -2.9916,
            'price_range' => '$$$',
            'opening_hours' => [
                'Monday' => 'Closed',
                'Tuesday' => 'Closed',
                'Wednesday' => '10:00 PM - 3:00 AM',
                'Thursday' => '10:00 PM - 3:00 AM',
                'Friday' => '10:00 PM - 4:00 AM',
                'Saturday' => '10:00 PM - 4:00 AM',
                'Sunday' => '10:00 PM - 2:00 AM',
            ],
            'amenities' => [
                'VIP Area' => 'Exclusive VIP section with bottle service and premium seating',
                'Dance Floor' => 'Massive dance floor with Funktion-One sound system',
                'Bar' => 'Full bar with premium spirits, cocktails, and craft beers',
                'Smoking Area' => 'Outdoor smoking terrace with heating',
                'Dress Code' => 'Smart casual - no sportswear or trainers',
                'Age Policy' => '18+ with valid ID required',
            ],
            'is_active' => true,
            'featured' => true,
            'user_id' => $electrikModerator->id,
        ]);

        // Create Level venue
        $level = Venue::create([
            'name' => 'Level',
            'description' => 'A sophisticated nightclub in the heart of Liverpool, offering an upscale clubbing experience with premium cocktails, exclusive events, and a stylish atmosphere. Perfect for special occasions and VIP experiences.',
            'address' => 'Seel Street',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 4AU',
            'country' => 'UK',
            'phone' => '+44 151 707 1000',
            'email' => 'info@level-liverpool.com',
            'website' => 'https://level-liverpool.com',
            'latitude' => 53.4048,
            'longitude' => -2.9883,
            'price_range' => '$$$$',
            'opening_hours' => [
                'Monday' => 'Closed',
                'Tuesday' => 'Closed',
                'Wednesday' => '9:00 PM - 3:00 AM',
                'Thursday' => '9:00 PM - 3:00 AM',
                'Friday' => '9:00 PM - 4:00 AM',
                'Saturday' => '9:00 PM - 4:00 AM',
                'Sunday' => 'Closed',
            ],
            'amenities' => [
                'VIP Lounge' => 'Private VIP area with dedicated service and premium seating',
                'Cocktail Bar' => 'Expert mixologists crafting premium cocktails and signature drinks',
                'Dance Floor' => 'Intimate dance space with high-end sound and lighting',
                'Bottle Service' => 'Premium bottle service with mixers and ice',
                'Dress Code' => 'Smart dress code - no casual wear',
                'Reservations' => 'Table reservations available for groups',
            ],
            'is_active' => true,
            'featured' => true,
            'user_id' => $levelModerator->id,
        ]);

        // Create Boom Battle Bar venue
        $boomBattleBar = Venue::create([
            'name' => 'Boom Battle Bar',
            'description' => 'The ultimate entertainment destination combining competitive socializing with great drinks and food. Features axe throwing, shuffleboard, beer pong, and other exciting games in a vibrant atmosphere.',
            'address' => 'Bold Street',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 4JA',
            'country' => 'UK',
            'phone' => '+44 151 709 2000',
            'email' => 'info@boombattlebar.com',
            'website' => 'https://boombattlebar.com',
            'latitude' => 53.4044,
            'longitude' => -2.9789,
            'price_range' => '$$',
            'opening_hours' => [
                'Monday' => '4:00 PM - 12:00 AM',
                'Tuesday' => '4:00 PM - 12:00 AM',
                'Wednesday' => '4:00 PM - 12:00 AM',
                'Thursday' => '4:00 PM - 1:00 AM',
                'Friday' => '4:00 PM - 2:00 AM',
                'Saturday' => '12:00 PM - 2:00 AM',
                'Sunday' => '12:00 PM - 12:00 AM',
            ],
            'amenities' => [
                'Axe Throwing' => 'Safe axe throwing lanes with professional instruction',
                'Shuffleboard' => 'Multiple shuffleboard tables for group play',
                'Beer Pong' => 'Beer pong tables with tournament play available',
                'Food Menu' => 'American-style food including burgers, wings, and sharing platters',
                'Bar' => 'Full bar with craft beers, cocktails, and soft drinks',
                'Group Bookings' => 'Perfect for birthday parties, corporate events, and group celebrations',
                'Age Policy' => '18+ after 8 PM, family-friendly during day',
            ],
            'is_active' => true,
            'featured' => false,
            'user_id' => $boomModerator->id,
        ]);

        // Update moderators to have venue_id
        $electrikModerator->update(['venue_id' => $electrikWarehouse->id]);
        $levelModerator->update(['venue_id' => $level->id]);
        $boomModerator->update(['venue_id' => $boomBattleBar->id]);

        $this->command->info('Liverpool venues and moderators created successfully!');
        $this->command->info('Electrik Warehouse - Manager: manager@electrikwarehouse.com');
        $this->command->info('Level - Manager: manager@level-liverpool.com');
        $this->command->info('Boom Battle Bar - Manager: manager@boombattlebar.com');
    }
}