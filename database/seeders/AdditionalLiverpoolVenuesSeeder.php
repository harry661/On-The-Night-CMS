<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Venue;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdditionalLiverpoolVenuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venueModeratorRole = Role::where('name', 'venue_moderator')->first();

        // The Jacaranda
        $jacarandaManager = User::create([
            'name' => 'The Jacaranda Manager',
            'email' => 'manager@jacaranda-liverpool.com',
            'password' => Hash::make('password'),
        ]);
        $jacarandaManager->assignRole($venueModeratorRole);

        $jacaranda = Venue::create([
            'name' => 'The Jacaranda',
            'description' => 'Historic music venue where The Beatles played their earliest gigs, now serving as a bar and entertainment space.',
            'address' => '21-23 Slater St',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 4BW',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 3472',
            'email' => 'info@jacaranda-liverpool.com',
            'website' => 'https://jacaranda-liverpool.com',
                'Monday' => '12:00 PM - 11:00 PM',
                'Tuesday' => '12:00 PM - 11:00 PM',
                'Wednesday' => '12:00 PM - 11:00 PM',
                'Thursday' => '12:00 PM - 12:00 AM',
                'Friday' => '12:00 PM - 1:00 AM',
                'Saturday' => '12:00 PM - 1:00 AM',
                'Sunday' => '12:00 PM - 11:00 PM',
            ],
            'amenities' => [
                'Beatles History' => 'Original Beatles performance venue',
                'Live Music' => 'Regular live performances',
                'Historic Atmosphere' => 'Authentic 1950s decor',
                'Music Memorabilia' => 'Beatles and music history displays',
                'Cocktail Bar' => 'Premium drinks selection',
            ],
            'is_active' => 'true',
            'featured' => true,
            'user_id' => $jacarandaManager->id,
        ]);
        $jacarandaManager->update(['venue_id' => $jacaranda->id]);

        // The Grapes
        $grapesManager = User::create([
            'name' => 'The Grapes Manager',
            'email' => 'manager@thegrapes-liverpool.com',
            'password' => Hash::make('password'),
        ]);
        $grapesManager->assignRole($venueModeratorRole);

        $grapes = Venue::create([
            'name' => 'The Grapes',
            'description' => 'Traditional British pub serving craft beers, ales, and hearty pub food in a cosy atmosphere.',
            'address' => '12 Roscoe St',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 2SX',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 4051',
            'email' => 'info@thegrapes-liverpool.com',
            'website' => 'https://thegrapes-liverpool.com',
                'Monday' => '11:00 AM - 11:00 PM',
                'Tuesday' => '11:00 AM - 11:00 PM',
                'Wednesday' => '11:00 AM - 11:00 PM',
                'Thursday' => '11:00 AM - 11:00 PM',
                'Friday' => '11:00 AM - 12:00 AM',
                'Saturday' => '11:00 AM - 12:00 AM',
                'Sunday' => '11:00 AM - 11:00 PM',
            ],
            'amenities' => [
                'Traditional Pub' => 'Classic British pub atmosphere',
                'Craft Beer' => 'Selection of independent ales and beers',
                'Pub Food' => 'Traditional British cuisine',
                'Quiz Nights' => 'Regular quiz and entertainment events',
                'Beer Garden' => 'Outdoor seating area',
            ],
            'is_active' => true,
            'featured' => false,
            'user_id' => $grapesManager->id,
        ]);
        $grapesManager->update(['venue_id' => $grapes->id]);

        // Alma de Cuba
        $almaManager = User::create([
            'name' => 'Alma de Cuba Manager',
            'email' => 'manager@almadecuba-liverpool.com',
            'password' => Hash::make('password'),
        ]);
        $almaManager->assignRole($venueModeratorRole);

        $alma = Venue::create([
            'name' => 'Alma de Cuba',
            'description' => 'Award-winning Cuban restaurant and bar with vibrant Latin American atmosphere, cocktails, and live music.',
            'address' => 'Seel St',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 4BH',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 4053',
            'email' => 'info@almadecuba-liverpool.com',
            'website' => '=https://almadecuba-liverpool.com',
                'Wednesday' => '5:00 PM - 12:00 AM',
                'Thursday' => '5:00 PM - 12:00 AM',
                'Friday' => '5:00 PM - 1:00 AM',
                'Saturday' => '5:00 PM - 1:00 AM',
                'Sunday' => '5:00 PM - 11:00 PM',
            ],
            'amenities' => [
                'Cuban Cuisine' => 'Authentic Latin American dishes',
                'Cocktail Bar' => 'Specialty Cuban and Latin cocktails',
                'Live Music' => 'Regular Latin music performances',
                'Dance Floor' => 'Space for dancing and entertainment',
                'Private Dining' => 'Private event hosting capabilities',
            ],
            'is_active' => true,
            'featured' => true,
            'user_id' => $almaManager->id,
        ]);
        $almaManager->update(['venue_id' => $alma->id]);

        // The Ship & Mitre
        $shipManager = User::create([
            'name' => 'The Ship & Mitre Manager',
            'email' => 'manager@shipmitre-liverpool.com',
            'password' => Hash::make('password'),
        ]);
        $shipManager->assignRole($venueModeratorRole);

        $ship = Venue::create([
            'name' => 'The Ship & Mitre',
            'description' => 'Traditional ale house featuring an extensive selection of real ales, ciders, and traditional pub entertainment.',
            'address' => '133 Dale St',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L2 2JH',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 4052',
            'email' => 'info@shipmitre-liverpool.com',
            'website' => 'https://shipmitre-liverpool.com',
                'Monday' => '11:00 AM - 11:00 PM',
                'Tuesday' => '11:00 AM - 11:00 PM',
                'Wednesday' => '11:00 AM - 11:00 PM',
                'Thursday' => '11:00 AM - 11:00 PM',
                'Friday' => '11:00 AM - 12:00 AM',
                'Saturday' => '11:00 AM - 12:00 AM',
                'Sunday' => '11:00 AM - 11:00 PM',
            ],
            'amenities' => [
                'Real Ales' => 'Extensive selection of local and national ales',
                'Traditional Pub' => 'Classic British pub experience',
                'Quiz Night' => 'Weekly quiz and entertainment',
                'Beer Garden' => 'Outdoor drinking area',
                'CAMRA Recommended' => 'Campaign for Real Ale accredited',
            ],
            'is_active' => true,
            'featured' => false,
            'user_id' => $shipManager->id,
        ]);
        $shipManager->update(['venue_id' => $ship->id]);

        $this->command->info('Additional Liverpool venues and moderators created successfully!');
        $this->command->info('The Jacaranda - Manager: manager@jacaranda-liverpool.com');
        $this->command->info('The Grapes - Manager: manager@thegrapes-liverpool.com');
        $this->command->info('Alma de Cuba - Manager: manager@almadecuba-liverpool.com');
        $this->command->info('The Ship & Mitre - Manager: manager@shipmitre-liverpool.com');
    }
}
