<?php

namespace Database\Seeders;

use App\Models\Sauce;
use Illuminate\Database\Seeder;

class SauceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sauces = [
            [
                'name' => 'Sốt Cay Hàn',
                'slug' => 'korean_spicy',
                'icon' => '🌶️',
                'tag' => '🌶️ Vị cay đặc trưng',
                'subtitle' => 'Cay nhẹ, ngọt hậu, thơm nồng ớt Gochujang Hàn Quốc.',
                'short_desc' => 'Đậm đà cay',
                'description' => 'Là sốt đặc trưng làm nên thương hiệu GAO. Gà giòn tan quyện cùng nước sốt sánh mịn óng ả, phủ đều từng thớ thịt.',
                'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=900&q=80',
                'price' => 49000,
                'is_active' => true,
            ],
            [
                'name' => 'Sốt Mật Ong',
                'slug' => 'honey_butter',
                'icon' => '🍯',
                'tag' => '🍯 Ngọt dịu thanh tao',
                'subtitle' => 'Vị ngọt ngào từ mật ong rừng hòa quyện bơ béo thơm lừng.',
                'short_desc' => 'Ngọt dịu thơm bơ',
                'description' => 'Sốt mật ong vàng óng ánh, thấm đẫm lớp vỏ gà chiên giòn tan, vị ngọt ngào dễ ăn phù hợp cho cả trẻ nhỏ.',
                'image' => 'https://images.unsplash.com/photo-1527477321055-43615862e771?auto=format&fit=crop&w=900&q=80',
                'price' => 49000,
                'is_active' => true,
            ],
            [
                'name' => 'Sốt Bơ Tỏi',
                'slug' => 'garlic_butter',
                'icon' => '🧄',
                'tag' => '🧄 Thơm nức mũi',
                'subtitle' => 'Hương tỏi phi vàng rụm kết hợp bơ thực vật béo ngậy.',
                'short_desc' => 'Béo ngậy thơm lừng',
                'description' => 'Tỏi phi giòn rụm rắc đều trên từng miếng gà phủ sốt bơ bóng bẩy. Mùi thơm nức mũi kích thích trọn vẹn vị giác.',
                'image' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=900&q=80',
                'price' => 49000,
                'is_active' => true,
            ],
            [
                'name' => 'Sốt Chua Ngọt',
                'slug' => 'sweet_sour',
                'icon' => '🥭',
                'tag' => '🥭 Chua ngọt đậm đà',
                'subtitle' => 'Vị chua ngọt hoa quả tươi mát kích thích vị giác.',
                'short_desc' => 'Chua ngọt thơm mát',
                'description' => 'Lớp sốt chua ngọt bóng bẩy với sốt me dứa đậm đà, ăn nhiều không ngấy, hương vị tươi mát độc đáo.',
                'image' => 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=900&q=80',
                'price' => 49000,
                'is_active' => true,
            ],
        ];

        foreach ($sauces as $sauce) {
            Sauce::updateOrCreate(['slug' => $sauce['slug']], $sauce);
        }
    }
}
