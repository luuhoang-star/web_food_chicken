<?php

namespace Database\Seeders;

use App\Models\SpiceLevel;
use Illuminate\Database\Seeder;

class SpiceLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Không cay',
                'description' => 'Dành cho người không ăn ớt',
                'level' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Cay nhẹ (Chuẩn vị)',
                'description' => 'Hơi tê tê đầu lưỡi, chuẩn vị GAO',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Cay vừa',
                'description' => 'Vị cay ấm nồng đậm đà',
                'level' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Cay nhiều 🔥',
                'description' => 'Thách thức tín đồ ăn cay',
                'level' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($levels as $level) {
            SpiceLevel::updateOrCreate(['name' => $level['name']], $level);
        }
    }
}
