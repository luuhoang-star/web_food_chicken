<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('testimonials')->truncate();
        Schema::enableForeignKeyConstraints();

        $reviews = [
            [
                'customer_name' => 'Minh Anh',
                'avatar' => 'MA',
                'avatar_bg' => 'bg-rose-200 text-rose-800',
                'content' => 'Gà giòn rụm mà sốt cay ngọt đỉnh chóp. Cơm dẻo thơm, dưa chua vừa vị. 10/10 điểm cho bữa trưa văn phòng!',
                'rating' => 5,
                'location' => 'Nhân viên văn phòng Cầu Giấy',
                'verified' => true,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'customer_name' => 'Đức Tuấn',
                'avatar' => 'ĐT',
                'avatar_bg' => 'bg-amber-200 text-amber-800',
                'content' => 'Ship đến gà vẫn giòn và nóng hổi. Hộp đóng gói sạch đẹp, sốt bơ tỏi thơm ngất ngây. Sẽ ủng hộ dài dài!',
                'rating' => 5,
                'location' => 'Sinh viên ĐH Quốc Gia Hà Nội',
                'verified' => true,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'customer_name' => 'Phương Linh',
                'avatar' => 'PL',
                'avatar_bg' => 'bg-purple-200 text-purple-800',
                'content' => 'Ăn combo duo với bạn gái ăn không hết luôn, quá nhiều gà mà giá siêu hạt dẻ. Sốt cay Hàn ăn dính lắm.',
                'rating' => 5,
                'location' => 'Kế toán tại Ba Đình',
                'verified' => true,
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($reviews as $review) {
            Testimonial::create($review);
        }
    }
}
