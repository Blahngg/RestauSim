<?php

namespace Database\Seeders;

use App\Models\UnitOfMeasurement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitOfMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = 
        [
            [
                'name' => 'gram',
                'symbol' => 'g',
                'category' => 'weight'
            ],
            [
                'name' => 'kilogram',
                'symbol' => 'kg',
                'category' => 'weight'
            ],
            [
                'name' => 'ounce',
                'symbol' => 'oz',
                'category' => 'weight'
            ],
            [
                'name' => 'pound',
                'symbol' => 'lb',
                'category' => 'weight'
            ],
            [
                'name' => 'milligram',
                'symbol' => 'mg',
                'category' => 'weight'
            ],
            [
                'name' => 'milliliter',
                'symbol' => 'ml',
                'category' => 'volume'
            ],
            [
                'name' => 'liter',
                'symbol' => 'l',
                'category' => 'volume'
            ],
            [
                'name' => 'teaspoon',
                'symbol' => 'tsp',
                'category' => 'volume'
            ],
            [
                'name' => 'tablespoon',
                'symbol' => 'tbsp',
                'category' => 'volume'
            ],
            [
                'name' => 'fluid ounce',
                'symbol' => 'fl oz',
                'category' => 'volume'
            ],
            [
                'name' => 'cup',
                'symbol' => 'cup',
                'category' => 'volume'
            ],
            [
                'name' => 'pint',
                'symbol' => 'pt',
                'category' => 'volume'
            ],
            [
                'name' => 'quart',
                'symbol' => 'qt',
                'category' => 'volume'
            ],
            [
                'name' => 'gallon',
                'symbol' => 'gal',
                'category' => 'volume'
            ],
            [
                'name' => 'piece',
                'symbol' => 'pc',
                'category' => 'count'
            ],
        ];

        foreach($units as $unit){
            UnitOfMeasurement::create([
                'name' => $unit['name'],
                'symbol' => $unit['symbol'],
                'category' => $unit['category']
            ]);
        }
    }
}
