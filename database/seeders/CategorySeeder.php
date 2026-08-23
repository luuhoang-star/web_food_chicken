<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cơm Gà',
                'slug' => 'rice',
                'icon' => '🍚',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Gà',
                'slug' => 'chicken',
                'icon' => '🍗',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Combo',
                'slug' => 'combo',
                'icon' => '🍱',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Ăn Kèm',
                'slug' => 'side',
                'icon' => '🍟',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Đồ Uống',
                'slug' => 'drink',
                'icon' => '🥤',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
