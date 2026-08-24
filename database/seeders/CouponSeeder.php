<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'GAO15K',
                'name' => 'Ưu đãi bạn mới giảm 15.000đ',
                'type' => 'fixed',
                'value' => 15000,
                'min_order_amount' => 80000,
                'max_discount' => null,
                'usage_limit' => 1000,
                'used_count' => 12,
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP50',
                'name' => 'Giảm 10% tối đa 30k đơn từ 120k',
                'type' => 'percent',
                'value' => 10,
                'min_order_amount' => 120000,
                'max_discount' => 30000,
                'usage_limit' => 500,
                'used_count' => 45,
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }
}
