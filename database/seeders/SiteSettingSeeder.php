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

            // Delivery & Shipping
            ['key' => 'shipping_fee_default', 'value' => '15000', 'group' => 'delivery', 'type' => 'number', 'description' => 'Phí ship mặc định'],
            ['key' => 'freeship_threshold', 'value' => '100000', 'group' => 'delivery', 'type' => 'number', 'description' => 'Ngưỡng đơn freeship'],
            ['key' => 'delivery_estimated_time', 'value' => '25 – 40 phút', 'group' => 'delivery', 'type' => 'text', 'description' => 'Thời gian giao hàng dự kiến'],

            // Payment & VietQR
            ['key' => 'payment_cod_enabled', 'value' => '1', 'group' => 'payment', 'type' => 'boolean', 'description' => 'Bật thanh toán COD'],
            ['key' => 'payment_bank_enabled', 'value' => '1', 'group' => 'payment', 'type' => 'boolean', 'description' => 'Bật quét VietQR'],
            ['key' => 'payment_momo_enabled', 'value' => '1', 'group' => 'payment', 'type' => 'boolean', 'description' => 'Bật ví MoMo'],
            ['key' => 'bank_code', 'value' => 'MB', 'group' => 'payment', 'type' => 'text', 'description' => 'Mã ngân hàng VietQR'],
            ['key' => 'bank_name', 'value' => 'MB Bank (Quân Đội)', 'group' => 'payment', 'type' => 'text', 'description' => 'Tên ngân hàng'],
            ['key' => 'bank_account_number', 'value' => '0988888888', 'group' => 'payment', 'type' => 'text', 'description' => 'Số tài khoản ngân hàng'],
            ['key' => 'bank_account_holder', 'value' => 'GAO CHICKEN HA NOI', 'group' => 'payment', 'type' => 'text', 'description' => 'Chủ tài khoản'],
            ['key' => 'bank_transfer_prefix', 'value' => 'HUBBY', 'group' => 'payment', 'type' => 'text', 'description' => 'Tiền tố nội dung chuyển khoản'],

            // Marketing & Popup
            ['key' => 'popup_enabled', 'value' => '0', 'group' => 'marketing', 'type' => 'boolean', 'description' => 'Bật popup khuyến mãi trang chủ'],
            ['key' => 'popup_title', 'value' => '🎉 Ưu Đãi Đặc Biệt Hôm Nay!', 'group' => 'marketing', 'type' => 'text', 'description' => 'Tiêu đề popup'],
            ['key' => 'popup_description', 'value' => 'Tặng ngay 01 hũ sốt đặc trưng hoặc Freeship 3km cho đơn hàng từ 100k hôm nay. Đặt ngay để nhận ưu đãi!', 'group' => 'marketing', 'type' => 'textarea', 'description' => 'Mô tả popup'],
            ['key' => 'popup_cta_text', 'value' => 'Xem Thực Đơn Đặt Ngay →', 'group' => 'marketing', 'type' => 'text', 'description' => 'Nút bấm popup'],
            ['key' => 'popup_cta_url', 'value' => '/menu', 'group' => 'marketing', 'type' => 'text', 'description' => 'Link popup'],
            ['key' => 'popup_banner_image', 'value' => '', 'group' => 'marketing', 'type' => 'text', 'description' => 'Ảnh banner popup'],

            // Footer & Brand
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

            // SEO & Tracking
            ['key' => 'meta_title', 'value' => 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị', 'group' => 'seo', 'type' => 'text', 'description' => 'SEO Title'],
            ['key' => 'meta_description', 'value' => 'Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội. Giao nhanh 25-40 phút.', 'group' => 'seo', 'type' => 'textarea', 'description' => 'SEO Description'],
            ['key' => 'meta_keywords', 'value' => 'gà sốt, gà rán hà nội, cơm gà sốt, gao gà rán', 'group' => 'seo', 'type' => 'text', 'description' => 'SEO Keywords'],
            ['key' => 'og_image', 'value' => '', 'group' => 'seo', 'type' => 'text', 'description' => 'Ảnh chia sẻ Facebook/Zalo'],
            ['key' => 'favicon_url', 'value' => '', 'group' => 'seo', 'type' => 'text', 'description' => 'Favicon URL'],
            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'seo', 'type' => 'text', 'description' => 'Google Analytics ID'],
            ['key' => 'facebook_pixel_id', 'value' => '', 'group' => 'seo', 'type' => 'text', 'description' => 'Facebook Pixel ID'],

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
