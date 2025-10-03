<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DrinkType;

class DrinkTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drinkTypes = [
            // Beer Category
            ['name' => 'Draught Beer', 'category' => 'Beer', 'description' => 'Fresh beer on tap', 'is_popular' => true],
            ['name' => 'Craft Beer', 'category' => 'Beer', 'description' => 'Artisanal microbrewery beers', 'is_popular' => true],
            ['name' => 'Lager', 'category' => 'Beer', 'description' => 'Light, crisp beer', 'is_popular' => true],
            ['name' => 'Ale', 'category' => 'Beer', 'description' => 'Traditional British ale', 'is_popular' => true],
            ['name' => 'Stout', 'category' => 'Beer', 'description' => 'Dark, rich beer', 'is_popular' => false],
            ['name' => 'IPA', 'category' => 'Beer', 'description' => 'India Pale Ale', 'is_popular' => false],

            // Spirits Category
            ['name' => 'Premium Spirits', 'category' => 'Spirits', 'description' => 'High-end spirits and liquors', 'is_popular' => true],
            ['name' => 'Whiskey', 'category' => 'Spirits', 'description' => 'Scotch, bourbon, and other whiskeys', 'is_popular' => true],
            ['name' => 'Vodka', 'category' => 'Spirits', 'description' => 'Premium and flavored vodkas', 'is_popular' => true],
            ['name' => 'Gin', 'category' => 'Spirits', 'description' => 'Artisanal and craft gins', 'is_popular' => true],
            ['name' => 'Rum', 'category' => 'Spirits', 'description' => 'Dark, white, and spiced rum', 'is_popular' => false],
            ['name' => 'Tequila', 'category' => 'Spirits', 'description' => 'Premium tequila selections', 'is_popular' => false],

            // Cocktails Category
            ['name' => 'Classic Cocktails', 'category' => 'Cocktails', 'description' => 'Traditional cocktails like martini, old fashioned', 'is_popular' => true],
            ['name' => 'Signature Cocktails', 'category' => 'Cocktails', 'description' => 'House specialty cocktails', 'is_popular' => true],
            ['name' => 'Frozen Cocktails', 'category' => 'Cocktails', 'description' => 'Blended frozen drinks', 'is_popular' => false],
            ['name' => 'Low Alcohol Cocktails', 'category' => 'Cocktails', 'description' => 'Light alcoholic cocktails', 'is_popular' => false],

            // Wine Category
            ['name' => 'Red Wine', 'category' => 'Wine', 'description' => 'Selection of red wines', 'is_popular' => true],
            ['name' => 'White Wine', 'category' => 'Wine', 'description' => 'Selection of white wines', 'is_popular' => true],
            ['name' => 'Sparkling Wine', 'category' => 'Wine', 'description' => 'Champagne and prosecco', 'is_popular' => true],
            ['name' => 'Rose Wine', 'category' => 'Wine', 'description' => 'Rosé wine selection', 'is_popular' => false],

            // Other Category
            ['name' => 'Soft Drinks', 'category' => 'Non-Alcoholic', 'description' => 'Coke, Pepsi, juices, etc.', 'is_popular' => true],
            ['name' => 'Energy Drinks', 'category' => 'Non-Alcoholic', 'description' => 'Red Bull, Monster, etc.', 'is_popular' => true],
            ['name' => 'Coffee', 'category' => 'Non-Alcoholic', 'description' => 'Espresso, cappuccino, latte', 'is_popular' => false],
            ['name' => 'Tea', 'category' => 'Non-Alcoholic', 'description' => 'Herbal and traditional teas', 'is_popular' => false],
        ];

        foreach ($drinkTypes as $drinkType) {
            DrinkType::create($drinkType);
        }
    }
}
