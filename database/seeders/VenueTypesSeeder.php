<?php

namespace Database\Seeders;

use App\Models\VenueType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenueTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venueTypes = [
            [
                'name' => 'Bar',
                'slug' => 'bar',
                'description' => 'Drinking establishments serving alcoholic beverages, cocktails, and often light snacks.',
                'icon' => 'beaker',
                'color' => '#8B5CF6',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Food',
                'slug' => 'food',
                'description' => 'Restaurants, cafes, and food establishments serving meals and beverages.',
                'icon' => 'cake',
                'color' => '#F59E0B',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Stay',
                'slug' => 'stay',
                'description' => 'Hotels, hostels, and accommodation venues for overnight stays.',
                'icon' => 'building-office',
                'color' => '#10B981',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Leisure',
                'slug' => 'leisure',
                'description' => 'Entertainment venues, clubs, theaters, and recreational facilities.',
                'icon' => 'musical-note',
                'color' => '#EF4444',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($venueTypes as $venueType) {
            VenueType::updateOrCreate(
                ['slug' => $venueType['slug']],
                $venueType
            );
        }
    }
}
