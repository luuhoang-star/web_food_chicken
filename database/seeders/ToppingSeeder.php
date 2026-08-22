<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toppings = [
            [
                'name' => 'Trứng Ốp La Lòng Đào',
                'price' => 10000,
                'icon' => '🍳',
                'is_active' => true,
            ],
            [
                'name' => 'Phô Mai Mozzarella Kéo Sợi',
                'price' => 15000,
                'icon' => '🧀',
                'is_active' => true,
            ],
            [
                'name' => 'Khoai Tây Chiên Giòn',
                'price' => 20000,
                'icon' => '🍟',
                'is_active' => true,
            ],
            [
                'name' => 'Thêm Sốt Chấm Riêng',
                'price' => 8000,
                'icon' => '🥣',
                'is_active' => true,
            ],
        ];

        foreach ($toppings as $topping) {
            Topping::updateOrCreate(['name' => $topping['name']], $topping);
        }
    }
}
