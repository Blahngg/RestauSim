<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = 
        [
            'Meat/Poultry',
            'Dairy/Eggs',
            'Produce',
            'Dry Goods/Grains',
            'Condiments/Sauces',
            'Spices/Seasonings',
            'Beverages',
        ];

        foreach ($categories as $category){
            InventoryCategory::create(['name' => $category]);
        }
    }
}
