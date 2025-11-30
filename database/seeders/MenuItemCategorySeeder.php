<?php

namespace Database\Seeders;

use App\Models\MenuItemCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = 
        [
            'Starters/Appetizers',
            'Soups & Salads',
            'Main Courses',
            'Pasta & Risotto',
            'Sides / Accompaniments',
            'Desserts',
            'Beverages',
        ];

        foreach ($categories as $category){
            MenuItemCategory::create(['name' => $category]);
        }
    }
}
