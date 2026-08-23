<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('site_settings')->truncate();
        Schema::enableForeignKeyConstraints();

        $settings = [
            // Header & General
            ['key' => 'site_name', 'value' => 'GAO', 'group' => 'general', 'type' => 'text', 'description' => 'Tên thương hiệu'],
            ['key' => 'site_tagline', 'value' => 'GÀ SỐT & CƠM', 'group' => 'general', 'type' => 'text', 'description' => 'Khẩu hiệu thương hiệu'],
            ['key' => 'location_short', 'value' => 'Hà Nội', 'group' => 'general', 'type' => 'text', 'description' => 'Khu vực hoạt động'],
            ['key' => 'location_badge', 'value' => 'Hà Nội (3–5km)', 'group' => 'header', 'type' => 'text', 'description' => 'Bán kính giao hàng'],
            ['key' => 'hotline', 'value' => '0988.868.GAO', 'group' => 'contact', 'type' => 'text', 'description' => 'Hotline đặt món'],
            ['key' => 'top_notification', 'value' => 'Ưu đãi hôm nay: Freeship bán kính 3km cho đơn hàng từ 100k!', 'group' => 'header', 'type' => 'text', 'description' => 'Thanh thông báo đầu trang'],
            ['key' => 'header_cta_text', 'value' => 'Đặt món', 'group' => 'header', 'type' => 'text', 'description' => 'Chữ nút CTA trên Header'],
            ['key' => 'header_cta_url', 'value' => '/menu', 'group' => 'header', 'type' => 'text', 'description' => 'Đường dẫn CTA Header'],

            // Footer
            ['key' => 'footer_description', 'value' => 'Thương hiệu Gà Sốt & Cơm chuẩn vị tại Hà Nội. Gà giòn rụm, đẫm sốt đậm đà, phục vụ nóng hổi tận tay khách hàng trong bán kính 3–5km.', 'group' => 'footer', 'type' => 'textarea', 'description' => 'Mô tả footer'],
            ['key' => 'store_address', 'value' => 'Hà Nội: Đống Đa, Cầu Giấy, Hoàn Kiếm, Hai Bà Trưng, Ba Đình, Thanh Xuân.', 'group' => 'contact', 'type' => 'text', 'description' => 'Địa bàn phục vụ'],
            ['key' => 'opening_hours', 'value' => 'Giờ nhận đơn: 09:30 – 22:00 hàng ngày', 'group' => 'contact', 'type' => 'text', 'description' => 'Khung giờ mở cửa'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com', 'group' => 'social', 'type' => 'text', 'description' => 'Link Facebook'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com', 'group' => 'social', 'type' => 'text', 'description' => 'Link Instagram'],
            ['key' => 'social_tiktok', 'value' => 'https://tiktok.com', 'group' => 'social', 'type' => 'text', 'description' => 'Link TikTok'],
            ['key' => 'copyright', 'value' => '© 2026 GAO - Gà Sốt & Cơm Hà Nội. All rights reserved.', 'group' => 'footer', 'type' => 'text', 'description' => 'Bản quyền'],
            ['key' => 'footer_slogan', 'value' => 'Thực đơn gà sốt đậm vị chuẩn Hà Nội', 'group' => 'footer', 'type' => 'text', 'description' => 'Slogan chân trang'],

            // Chat & Support
            ['key' => 'contact_zalo', 'value' => '0973797151', 'group' => 'contact', 'type' => 'text', 'description' => 'Số Zalo tư vấn'],
            ['key' => 'contact_zalo_url', 'value' => 'https://zalo.me/0973797151', 'group' => 'contact', 'type' => 'text', 'description' => 'Đường dẫn Chat Zalo'],
            ['key' => 'contact_messenger_url', 'value' => 'https://m.me/luuhoang.it', 'group' => 'contact', 'type' => 'text', 'description' => 'Đường dẫn Chat Messenger'],
            ['key' => 'contact_facebook_url', 'value' => 'https://facebook.com/luuhoang.it', 'group' => 'contact', 'type' => 'text', 'description' => 'Đường dẫn Trang cá nhân Facebook'],

            // Telegram Order Notifications
            ['key' => 'telegram_bot_token', 'value' => '', 'group' => 'notification', 'type' => 'text', 'description' => 'Telegram Bot Token'],
            ['key' => 'telegram_chat_id', 'value' => '', 'group' => 'notification', 'type' => 'text', 'description' => 'Telegram Chat ID'],
            ['key' => 'telegram_notifications_enabled', 'value' => '1', 'group' => 'notification', 'type' => 'boolean', 'description' => 'Bật/Tắt thông báo Telegram'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::create($setting);
        }
    }
}
