<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MusicGenre;

class MusicGenresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $musicGenres = [
            // Electronic Category
            ['name' => 'House', 'category' => 'Electronic', 'description' => 'Progressive and deep house music', 'is_popular' => true],
            ['name' => 'Techno', 'category' => 'Electronic', 'description' => 'Industrial electronic beats', 'is_popular' => true],
            ['name' => 'Deep House', 'category' => 'Electronic', 'description' => 'Slow tempo electronic with soulful elements', 'is_popular' => true],
            ['name' => 'Trance', 'category' => 'Electronic', 'description' => 'Euphoric electronic dance music', 'is_popular' => false],
            ['name' => 'Drum & Bass', 'category' => 'Electronic', 'description' => 'Fast breakbeat electronic music', 'is_popular' => false],
            ['name' => 'Dubstep', 'category' => 'Electronic', 'description' => 'Heavy electronic bass music', 'is_popular' => false],

            // Commercial Category
            ['name' => 'Chart Hits', 'category' => 'Commercial', 'description' => 'Current popular commercial music', 'is_popular' => true],
            ['name' => 'Pop', 'category' => 'Commercial', 'description' => 'Popular mainstream pop music', 'is_popular' => true],
            ['name' => 'R&B', 'category' => 'Commercial', 'description' => 'Rhythm and blues music', 'is_popular' => true],
            ['name' => 'Commercial House', 'category' => 'Commercial', 'description' => 'Mainstream house music', 'is_popular' => true],
            ['name' => 'Dance Pop', 'category' => 'Commercial', 'description' => 'Pop music with dance elements', 'is_popular' => false],

            // Hip-Hop Category
            ['name' => 'Hip-Hop', 'category' => 'Hip-Hop', 'description' => 'Traditional hip-hop and rap', 'is_popular' => true],
            ['name' => 'Trap', 'category' => 'Hip-Hop', 'description' => 'Modern trap music', 'is_popular' => true],
            ['name' => 'Grime', 'category' => 'Hip-Hop', 'description' => 'UK hip-hop style', 'is_popular' => false],
            ['name' => 'Alternative Hip-Hop', 'category' => 'Hip-Hop', 'description' => 'Non-mainstream hip-hop', 'is_popular' => false],

            // Rock Category
            ['name' => 'Indie Rock', 'category' => 'Rock', 'description' => 'Independent rock music', 'is_popular' => true],
            ['name' => 'Alternative Rock', 'category' => 'Rock', 'description' => 'Non-mainstream rock', 'is_popular' => true],
            ['name' => 'Rock', 'category' => 'Rock', 'description' => 'Classic and modern rock', 'is_popular' => true],
            ['name' => 'Punk Rock', 'category' => 'Rock', 'description' => 'Fast, aggressive rock', 'is_popular' => false],
            ['name' => 'Metal', 'category' => 'Rock', 'description' => 'Heavy metal music', 'is_popular' => false],

            // Other Categories
            ['name' => 'Jazz', 'category' => 'Other', 'description' => 'Traditional and modern jazz', 'is_popular' => false],
            ['name' => 'Funk', 'category' => 'Other', 'description' => 'Funk and soul music', 'is_popular' => false],
            ['name' => 'Disco', 'category' => 'Other', 'description' => 'Classic dance disco', 'is_popular' => false],
            ['name' => 'Ska', 'category' => 'Other', 'description' => 'Jamaican ska music', 'is_popular' => false],
            ['name' => 'Reggae', 'category' => 'Other', 'description' => 'Jamaican reggae music', 'is_popular' => false],
            ['name' => 'Motown', 'category' => 'Other', 'description' => 'Classic soul and Motown', 'is_popular' => false],
        ];

        foreach ($musicGenres as $musicGenre) {
            MusicGenre::create($musicGenre);
        }
    }
}
