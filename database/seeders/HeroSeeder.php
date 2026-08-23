<?php

namespace Database\Seeders;

use App\Models\Hero;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HeroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('heroes')->truncate();
        Schema::enableForeignKeyConstraints();

        Hero::create([
            'badge' => '✦ GÀ CHIÊN + SỐT ĐẬM VỊ',
            'title' => 'GÀ GIÒN.',
            'title_highlight' => 'SỐT ĐẬM.',
            'subtitle' => 'Gà nóng giòn, phủ sốt nguyên bản. Giao tận nơi tại Hà Nội trong 25–40 phút.',
            'cta_primary_text' => '🍗 ĐẶT MÓN NGAY',
            'cta_primary_url' => '/menu',
            'cta_secondary_text' => '🔥 XEM 4 VỊ SỐT',
            'cta_secondary_url' => '/menu',
            'delivery_time' => 'Giao 25–40p',
            'hot_status' => 'Luôn nóng giòn',
            'rating' => 'Đánh giá 4.9/5',
            'image' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=1000&q=85',
            'price' => 49000,
            'floating_badge' => '🔥 MÓN MỚI RA MẮT • ĐẶT NGAY',
            'is_active' => true,
            'order' => 1,
        ]);
    }
}
