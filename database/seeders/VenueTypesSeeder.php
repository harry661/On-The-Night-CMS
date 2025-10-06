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
                'icon' => 'beaker',
                'color' => '#8B5CF6',
                'is_active' => true,
            ],
            [
                'name' => 'Food',
                'icon' => 'cake',
                'color' => '#F59E0B',
                'is_active' => true,
            ],
            [
                'name' => 'Stay',
                'icon' => 'building-office',
                'color' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'Leisure',
                'icon' => 'musical-note',
                'color' => '#EF4444',
                'is_active' => true,
            ],
        ];

        foreach ($venueTypes as $venueType) {
            VenueType::updateOrCreate(
                ['name' => $venueType['name']],
                $venueType
            );
        }
    }
}
