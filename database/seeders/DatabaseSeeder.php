<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Venue;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $venueModeratorRole = Role::create(['name' => 'venue_moderator']);

        // Create permissions
        $permissions = [
            'view_any_venue',
            'view_venue',
            'create_venue',
            'update_venue',
            'delete_venue',
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        $venueModeratorRole->givePermissionTo([
            'view_venue',
            'update_venue',
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@onthenight.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

        // Create venue moderator user
        $moderator = User::create([
            'name' => 'Venue Moderator',
            'email' => 'moderator@onthenight.com',
            'password' => Hash::make('password'),
        ]);
        $moderator->assignRole($venueModeratorRole);

        // Create sample venue
        $venue = Venue::create([
            'name' => 'The Night Club',
            'description' => 'A premier nightclub in the heart of the city, featuring the best DJs and a vibrant atmosphere.',
            'address' => '123 Night Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1 (555) 123-4567',
            'email' => 'info@thenightclub.com',
            'website' => 'https://thenightclub.com',
            'latitude' => 40.7589,
            'longitude' => -73.9851,
            'price_range' => '$$$',
            'opening_hours' => [
                'Monday' => 'Closed',
                'Tuesday' => '9:00 PM - 2:00 AM',
                'Wednesday' => '9:00 PM - 2:00 AM',
                'Thursday' => '9:00 PM - 3:00 AM',
                'Friday' => '9:00 PM - 4:00 AM',
                'Saturday' => '9:00 PM - 4:00 AM',
                'Sunday' => '9:00 PM - 2:00 AM',
            ],
            'amenities' => [
                'VIP Area' => 'Exclusive VIP section with bottle service',
                'Dance Floor' => 'Large dance floor with state-of-the-art sound system',
                'Bar' => 'Full bar with premium spirits and cocktails',
                'Parking' => 'Valet parking available',
                'Dress Code' => 'Upscale casual attire required',
            ],
            'is_active' => true,
            'featured' => true,
            'user_id' => $moderator->id,
        ]);

        // Update moderator to have venue_id
        $moderator->update(['venue_id' => $venue->id]);

        // Seed additional Liverpool venues
        $this->call([
            LiverpoolVenuesSeeder::class,
            LiverpoolNightlifeSeeder::class,
            VenueImagesSeeder::class,
        ]);
    }
}

