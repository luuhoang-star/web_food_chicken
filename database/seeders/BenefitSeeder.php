<?php

namespace Database\Seeders;

use App\Models\Benefit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('benefits')->truncate();
        Schema::enableForeignKeyConstraints();

        $benefits = [
            [
                'icon' => '⏱️',
                'color_class' => 'bg-red-50 text-red-600',
                'title' => '25–40 phút',
                'description' => 'Giao nhanh nóng hổi nội thành Hà Nội',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'icon' => '🔥',
                'color_class' => 'bg-amber-50 text-amber-600',
                'title' => 'Làm mới mỗi ngày',
                'description' => 'Gà tươi chiên giòn, sốt nấu mới mỗi ngày',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'icon' => '🛵',
                'color_class' => 'bg-emerald-50 text-emerald-600',
                'title' => 'Freeship 3km',
                'description' => 'Áp dụng cho đơn từ 100k trở lên',
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($benefits as $benefit) {
            Benefit::create($benefit);
        }
    }
}
