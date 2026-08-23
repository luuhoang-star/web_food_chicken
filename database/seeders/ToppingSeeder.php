<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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
                'name' => 'Thêm Cơm Dẻo Nóng',
                'price' => 5000,
                'icon' => '🍚',
                'is_active' => true,
            ],
            [
                'name' => 'Thêm Gà Giòn (1 miếng)',
                'price' => 10000,
                'icon' => '🍗',
                'is_active' => true,
            ],
            [
                'name' => 'Phô Mai Mozzarella Kéo Sợi',
                'price' => 15000,
                'icon' => '🧀',
                'is_active' => true,
            ],
            [
                'name' => 'Kim Chi Hàn Quốc Thanh Mát',
                'price' => 10000,
                'icon' => '🥗',
                'is_active' => true,
            ],
            [
                'name' => 'Cốc Sốt Cay Hàn (Chấm thêm)',
                'price' => 8000,
                'icon' => '🌶️',
                'is_active' => true,
            ],
            [
                'name' => 'Cốc Sốt Mật Ong (Chấm thêm)',
                'price' => 8000,
                'icon' => '🍯',
                'is_active' => true,
            ],
            [
                'name' => 'Cốc Sốt Bơ Tỏi (Chấm thêm)',
                'price' => 8000,
                'icon' => '🧄',
                'is_active' => true,
            ],
            [
                'name' => 'Cốc Sốt Chua Ngọt (Chấm thêm)',
                'price' => 8000,
                'icon' => '🥭',
                'is_active' => true,
            ],
        ];

        Schema::disableForeignKeyConstraints();
        Topping::truncate();
        Schema::enableForeignKeyConstraints();

        foreach ($toppings as $topping) {
            Topping::create($topping);
        }
    }
}
