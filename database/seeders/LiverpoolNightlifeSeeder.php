<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Venue;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class LiverpoolNightlifeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venueModeratorRole = Role::where('name', 'venue_moderator')->first();

        // Heebie Jeebies
        $heebieJeebiesManager = User::create([
            'name' => 'Heebie Jeebies Manager',
            'email' => 'manager@heebiejeebies.com',
            'password' => Hash::make('password'),
        ]);
        $heebieJeebiesManager->assignRole($venueModeratorRole);

        $heebieJeebies = Venue::create([
            'name' => 'Heebie Jeebies',
            'description' => 'Liverpool\'s premier alternative club with three floors playing indie, rock and alternative music.',
            'address' => '86-88 Seel St',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 4BH',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 4054',
            'email' => 'info@heebiejeebies.com',
            'website' => 'https://heebiejeebies.co.uk',
                'Thursday' => '10:00 PM - 3:00 AM',
                'Friday' => '10:00 PM - 4:00 AM',
                'Saturday' => '10:00 PM - 4:00 AM',
                'Sunday' => '10:00 PM - 2:00 AM',
            ],
            'amenities' => [
                'Three Floors' => 'Diverse music genres across different levels',
                'Rock & Indie' => 'Alternative music focus',
                'Live Music' => 'Regular live band performances',
                'Student Night' => 'Special student events and discounts',
                'Themed Nights' => 'Rock, metal and indie themed events',
            ],
            'is_active' => true,
            'featured' => true,
            'user_id' => $heebieJeebiesManager->id,
        ]);
        $heebieJeebiesManager->update(['venue_id' => $heebieJeebies->id]);

        // Slug & Lettuce Manager
        $slugLettuceManager = User::create([
            'name' => 'Slug & Lettuce Manager',
            'email' => 'manager@sluglettuce-liverpool.com',
            'password' => Hash::make('password'),
        ]);
        $slugLettuceManager->assignRole($venueModeratorRole);

        $slugLettuce = Venue::create([
            'name' => 'Slug & Lettuce Liverpool',
            'description' => 'Contemporary bar and restaurant perfect for groups, serving cocktails, food and hosting events.',
            'address' => 'The Pavilion, Paradise St',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 8JF',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 3699',
            'email' => 'liverpool@sluglettuce.co.uk',
            'website' => 'https://sluglettuce.co.uk/liverpool',
                'Monday' => '11:00 AM - 11:00 PM',
                'Tuesday' => '11:00 AM - 11:00 PM',
                'Wednesday' => '11:00 AM - 11:00 PM',
                'Thursday' => '11:00 AM - 12:00 AM',
                'Friday' => '11:00 AM - 1:00 AM',
                'Saturday' => '11:00 AM - 1:00 AM',
                'Sunday' => '11:00 AM - 11:00 PM',
            ],
            'amenities' => [
                'Cocktail Bar' => 'Extensive cocktail menu',
                'Food Service' => 'Full restaurant menu',
                'Private Events' => 'Space for private functions',
                'Large Groups' => 'Perfect for group bookings',
                'Business Events' => 'Professional event hosting',
            ],
            'is_active' => true,
            'featured' => false,
            'user_id' => $slugLettuceManager->id,
        ]);
        $slugLettuceManager->update(['venue_id' => $slugLettuce->id]);

        // The Krazyhouse
        $krazyhouseManager = User::create([
            'name' => 'The Krazyhouse Manager',
            'email' => 'manager@krazyhouse.co.uk',
            'password' => Hash::make('password'),
        ]);
        $krazyhouseManager->assignRole($venueModeratorRole);

        $krazyhouse = Venue::create([
            'name' => 'The Krazyhouse',
            'description' => 'Two-floor club playing chart hits, R&B, and commercial dance music with themed nights.',
            'address' => 'Wood St',
            'city' => 'Liverpool', 
            'state' => 'Merseyside',
            'postal_code' => 'L1 4DQ',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 4066',
            'email' => 'info@krazyhouse.co.uk',
            'website' => 'https://krazyhouse.co.uk',
                'Wednesday' => '10:00 PM - 3:00 AM',
                'Thursday' => '10:00 PM - 3:00 AM',
                'Friday' => '10:00 PM - 4:00 AM',
                'Saturday' => '10:00 PM - 4:00 AM',
                'Sunday' => '10:00 PM - 2:00 AM',
            ],
            'amenities' => [
                'Two Floors' => 'Different music on each level',
                'Chart Hits' => 'Commercial and popular music',
                'R&B Nights' => 'Special R&B themed events',
                'Student Nights' => 'Student discounts and events',
                'VIP Area' => 'Private area with bottle service',
            ],
            'is_active' => true,
            'featured' => true,
            'user_id' => $krazyhouseManager->id,
        ]);
        $krazyhouseManager->update(['venue_id' => $krazyhouse->id]);

        // Brooklyn Mixer
        $brooklynMixerManager = User::create([
            'name' => 'Brooklyn Mixer Manager',
            'email' => 'manager@brooklynmixer.com',
            'password' => Hash::make('password'),
        ]);
        $brooklynMixerManager->assignRole($venueModeratorRole);

        $brooklynMixer = Venue::create([
            'name' => 'Brooklyn Mixer',
            'description' => 'Trendy underground bar with a New York-inspired atmosphere, craft cocktails and live DJs.',
            'address' => 'Seel St',
            'city' => 'Liverpool',
            'state' => 'Merseyside', 
            'postal_code' => 'L1 4BH',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 4055',
            'email' => 'hello@brooklynmixer.com',
            'website' => 'https://brooklynmixer.com',
                'Tuesday' => '6:00 PM - 12:00 AM',
                'Wednesday' => '6:00 PM - 12:00 AM',
                'Thursday' => '6:00 PM - 1:00 AM',
                'Friday' => '6:00 PM - 2:00 AM',
                'Saturday' => '6:00 PM - 2:00 AM',
                'Sunday' => '6:00 PM - 12:00 AM',
            ],
            'amenities' => [
                'Craft Cocktails' => 'Premium cocktail menu',
                'Underground Vibe' => 'Unique basement atmosphere',
                'Live DJs' => 'Regular DJ performances',
                'New York Theme' => 'Brooklyn-inspired decor',
                'Speakeasy' => 'Prohibition-era aesthetic',
            ],
            'is_active' => true,
            'featured' => false,
            'user_id' => $brooklynMixerManager->id,
        ]);
        $brooklynMixerManager->update(['venue_id' => $brooklynMixer->id]);

        // Revolution Liverpool
        $revolutionManager = User::create([
            'name' => 'Revolution Liverpool Manager',
            'email' => 'manager@revolution-liverpool.com',
            'password' => Hash::make('password'),
        ]);
        $revolutionManager->assignRole($venueModeratorRole);

        $revolution = Venue::create([
            'name' => 'Revolution Liverpool',
            'description' => 'Stylish cocktail bar chain with food, cocktails, and late-night entertainment.',
            'address' => '60-62 Hope St',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L1 9BX',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 3695',
            'email' => 'liverpool@revolution-bars.com',
            'website' => 'https://revolution-bars.com/liverpool',
                'Monday' => '12:00 PM - 12:00 AM',
                'Tuesday' => '12:00 PM - 12:00 AM',
                'Wednesday' => '12:00 PM - 12:00 AM',
                'Thursday' => '12:00 PM - 1:00 PM',
                'Friday' => '12:00 PM - 1:00 AM',
                'Saturday' => '12:00 PM - 1:00 AM',
                'Sunday' => '12:00 PM - 12:00 AM',
            ],
            'amenities' => [
                'Cocktail Bar' => 'Signature cocktail collection',
                'Food Service' => 'Asian-inspired dining',
                'Bottomless Brunch' => 'Award-winning brunch experience',
                'Private Dining' => 'Group and private bookings',
                'Late Night' => 'Extended evening hours',
            ],
            'is_active' => true,
            'featured' => false,
            'user_id' => $revolutionManager->id,
        ]);
        $revolutionManager->update(['venue_id' => $revolution->id]);

        // The Merchant
        $merchantManager = User::create([
            'name' => 'The Merchant Manager',
            'email' => 'manager@themerchant-liverpool.com',
            'password' => Hash::make('password'),
        ]);
        $merchantManager->assignRole($venueModeratorRole);

        $merchant = Venue::create([
            'name' => 'The Merchant',
            'description' => 'Award-winning cocktail bar and kitchen in Liverpool\'s historic Castle Street.',
            'address' => '8 Castle St',
            'city' => 'Liverpool',
            'state' => 'Merseyside',
            'postal_code' => 'L2 5SA',
            'country' => 'UK',
            'phone' => '+44 (0)151 709 3698',
            'email' => 'info@themerchant-liverpool.com',
            'website' => 'https://themerchant-liverpool.com',
                'Monday' => '5:00 PM - 12:00 AM',
                'Tuesday' => '5:00 PM - 12:00 AM',
                'Wednesday' => '5:00 PM - 12:00 AM',
                'Thursday' => '5:00 PM - 12:00 AM',
                'Friday' => '5:00 PM - 1:00 AM',
                'Saturday' => '5:00 PM - 1:00 AM',
                'Sunday' => '5:00 PM - 11:00 PM',
            ],
            'amenities' => [
                'Award-Winning Cocktails' => 'Bartenders\' awards recognition',
                'Historic Location' => 'Victorian-era building',
                'Gin Collection' => 'Extensive gin selection',
                'Food & Cocktails' => 'Modern British cuisine',
                'After Work' => 'Perfect for business networking',
            ],
            'is_active' => true,
            'featured' => true,
            'user_id' => $merchantManager->id,
        ]);
        $merchantManager->update(['venue_id' => $merchant->id]);

        $this->command->info('Liverpool nightlife venues and moderators created successfully!');
        $this->command->info('Heebie Jeebies - Manager: manager@heebiejeebies.com');
        $this->command->info('Slug & Lettuce Liverpool - Manager: manager@sluglettuce-liverpool.com');
        $this->command->info('The Krazyhouse - Manager: manager@krazyhouse.co.uk');
        $this->command->info('Brooklyn Mixer - Manager: manager@brooklynmixer.com');
        $this->command->info('Revolution Liverpool - Manager: manager@revolution-liverpool.com');
        $this->command->info('The Merchant - Manager: manager@themerchant-liverpool.com');
    }
}
