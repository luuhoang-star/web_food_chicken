@extends('layouts.admin')

@section('title', 'Trung Tâm Cài Đặt Hệ Thống & Marketing')
@section('page_title', '⚙️ Cài Đặt Hệ Thống & Marketing Toàn Diện')

@section('content')
<div class="max-w-5xl space-y-6" x-data="{ activeTab: 'general' }">

    <!-- TAB NAVIGATION BAR -->
    <div class="bg-white p-2 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-1.5 overflow-x-auto text-xs font-bold">
        <button 
            type="button" 
            @click="activeTab = 'general'" 
            :class="activeTab === 'general' ? 'bg-gray-900 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer"
        >
            <span>🏢</span>
            <span>Quán & Thông Tin</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'delivery'" 
            :class="activeTab === 'delivery' ? 'bg-gray-900 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer"
        >
            <span>🛵</span>
            <span>Giao Hàng & Phí Ship</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'payment'" 
            :class="activeTab === 'payment' ? 'bg-gray-900 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer"
        >
            <span>💳</span>
            <span>Thanh Toán & VietQR</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'marketing'" 
            :class="activeTab === 'marketing' ? 'bg-gray-900 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer"
        >
            <span>🎁</span>
            <span>Popup & Banner Sự Kiện</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'socials'" 
            :class="activeTab === 'socials' ? 'bg-gray-900 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer"
        >
            <span>💬</span>
            <span>Chat & Mạng Xã Hội</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'seo'" 
            :class="activeTab === 'seo' ? 'bg-gray-900 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer"
        >
            <span>🔍</span>
            <span>SEO & Mã Tracking</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'telegram'" 
            :class="activeTab === 'telegram' ? 'bg-gray-900 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer"
        >
            <span>🔔</span>
            <span>Telegram Bot</span>
        </button>
    </div>

    <!-- MAIN FORM -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- ==================== TAB 1: THÔNG TIN QUÁN & CHÂN TRANG ==================== -->
        <div x-show="activeTab === 'general'" class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🏢</span>
                        <span>Thông Tin Thương Hiệu & Bếp Quán</span>
                    </h3>
                    <p class="text-xs text-gray-500">Hiển thị trên Header, Footer và các trang giới thiệu.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tên Thương Hiệu (Logo Text)</label>
                        <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'GAO' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Slogan Nhỏ Dưới Logo</label>
                        <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] ?? 'GÀ SỐT & CƠM' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Hotline Bếp (Gọi đặt/hỗ trợ)</label>
                        <input type="text" name="hotline" value="{{ $settings['hotline'] ?? '0988.868.GAO' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Giờ Mở Cửa Nhận Đơn</label>
                        <input type="text" name="opening_hours" value="{{ $settings['opening_hours'] ?? 'Giờ nhận đơn: 09:30 – 22:00 hàng ngày' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Địa Bàn Phục Vụ / Danh Sách Chi Nhánh Bếp</label>
                        <input type="text" name="store_address" value="{{ $settings['store_address'] ?? 'Hà Nội: Đống Đa, Cầu Giấy, Hoàn Kiếm, Hai Bà Trưng, Ba Đình, Thanh Xuân.' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Đoạn Giới Thiệu Ngắn Ở Chân Trang (Footer)</label>
                        <textarea name="footer_description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none">{{ $settings['footer_description'] ?? 'Thương hiệu Gà Sốt & Cơm chuẩn vị tại Hà Nội. Gà giòn rụm, đẫm sốt đậm đà, phục vụ nóng hổi tận tay khách hàng trong bán kính 3–5km.' }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Bản Quyền Footer</label>
                        <input type="text" name="copyright" value="{{ $settings['copyright'] ?? '© 2026 GAO - Gà Sốt & Cơm Hà Nội. All rights reserved.' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Slogan Phụ Chân Trang</label>
                        <input type="text" name="footer_slogan" value="{{ $settings['footer_slogan'] ?? 'Thực đơn gà sốt đậm vị chuẩn Hà Nội' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 2: GIAO HÀNG & PHÍ SHIP ==================== -->
        <div x-show="activeTab === 'delivery'" class="space-y-6" x-cloak>
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🛵</span>
                        <span>Cấu Hình Giao Hàng & Phí Ship</span>
                    </h3>
                    <p class="text-xs text-gray-500">Tự động tính phí vận chuyển và miễn phí ship khi khách đạt mốc đơn.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Phí Ship Mặc Định (VNĐ)</label>
                        <div class="relative">
                            <input type="number" name="shipping_fee_default" value="{{ $settings['shipping_fee_default'] ?? '15000' }}" step="1000" min="0" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-red-600 outline-none">
                            <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-bold">đ</span>
                        </div>
                        <p class="text-[10px] text-gray-400">Áp dụng cho các đơn hàng chưa đạt mức Freeship.</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Ngưỡng Miễn Phí Ship - Freeship (VNĐ)</label>
                        <div class="relative">
                            <input type="number" name="freeship_threshold" value="{{ $settings['freeship_threshold'] ?? '100000' }}" step="1000" min="0" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-emerald-600 outline-none">
                            <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-bold">đ</span>
                        </div>
                        <p class="text-[10px] text-gray-400">Đơn hàng đạt từ số tiền này trở lên sẽ tự động được Freeship (0đ).</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Thời Gian Giao Dự Kiến</label>
                        <input type="text" name="delivery_estimated_time" value="{{ $settings['delivery_estimated_time'] ?? '25 – 40 phút' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Bán Kính Phục Vụ (Badge Header)</label>
                        <input type="text" name="location_badge" value="{{ $settings['location_badge'] ?? 'Hà Nội (3–5km)' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 3: THANH TOÁN & VIETQR ==================== -->
        <div x-show="activeTab === 'payment'" class="space-y-6" x-cloak>
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>💳</span>
                        <span>Cổng Thanh Toán & Cấu Hình VietQR Ngân Hàng</span>
                    </h3>
                    <p class="text-xs text-gray-500">Mã VietQR động trong checkout sẽ tự động kết nối theo số tài khoản bạn nhập dưới đây.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Bật/tắt phương thức -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tiền Mặt COD</label>
                        <select name="payment_cod_enabled" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                            <option value="1" {{ ($settings['payment_cod_enabled'] ?? '1') == '1' ? 'selected' : '' }}>✅ Cho phép thanh toán COD</option>
                            <option value="0" {{ ($settings['payment_cod_enabled'] ?? '1') == '0' ? 'selected' : '' }}>❌ Tắt COD</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Chuyển Khoản VietQR</label>
                        <select name="payment_bank_enabled" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                            <option value="1" {{ ($settings['payment_bank_enabled'] ?? '1') == '1' ? 'selected' : '' }}>✅ Bật Quét Mã VietQR</option>
                            <option value="0" {{ ($settings['payment_bank_enabled'] ?? '1') == '0' ? 'selected' : '' }}>❌ Tắt Chuyển Khoản</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Ví Điện Tử MoMo</label>
                        <select name="payment_momo_enabled" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                            <option value="1" {{ ($settings['payment_momo_enabled'] ?? '1') == '1' ? 'selected' : '' }}>✅ Bật MoMo</option>
                            <option value="0" {{ ($settings['payment_momo_enabled'] ?? '1') == '0' ? 'selected' : '' }}>❌ Tắt MoMo</option>
                        </select>
                    </div>

                    <!-- Thông tin Ngân hàng VietQR -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Mã Ngân Hàng (Bank Code)</label>
                        <input type="text" name="bank_code" value="{{ $settings['bank_code'] ?? 'MB' }}" placeholder="VD: MB, VCB, ACB, TCB, VPB, TPB..." class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-gray-900 outline-none uppercase font-mono">
                        <p class="text-[10px] text-gray-400">Mã ngân hàng chuẩn VietQR (MB, VCB, ACB, TCB...)</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tên Ngân Hàng Hiển Thị</label>
                        <input type="text" name="bank_name" value="{{ $settings['bank_name'] ?? 'MB Bank (Quân Đội)' }}" placeholder="VD: MB Bank" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Số Tài Khoản Nhận Tiền</label>
                        <input type="text" name="bank_account_number" value="{{ $settings['bank_account_number'] ?? '0988888888' }}" placeholder="VD: 0988888888" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-red-600 outline-none font-mono">
                    </div>

                    <div class="sm:col-span-3 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tên Chủ Tài Khoản (In hoa không dấu)</label>
                        <input type="text" name="bank_account_holder" value="{{ $settings['bank_account_holder'] ?? 'GAO CHICKEN HA NOI' }}" placeholder="VD: NGUYEN VAN A" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none uppercase">
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 4: POPUP & BANNER KHUYẾN MÃI ==================== -->
        <div x-show="activeTab === 'marketing'" class="space-y-6" x-cloak>
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🎁</span>
                        <span>Cấu Hình Popup Sự Kiện & Banner Ưu Đãi</span>
                    </h3>
                    <p class="text-xs text-gray-500">Tự động bật cửa sổ popup ưu đãi khi khách hàng vừa truy cập website.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Trạng Thái Popup Khi Khách Vào Web</label>
                        <select name="popup_enabled" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                            <option value="1" {{ ($settings['popup_enabled'] ?? '0') == '1' ? 'selected' : '' }}>✅ BẬT - Tự động hiện popup khuyến mãi khi vào web</option>
                            <option value="0" {{ ($settings['popup_enabled'] ?? '0') == '0' ? 'selected' : '' }}>❌ TẮT - Không hiện popup</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tiêu Đề Popup</label>
                        <input type="text" name="popup_title" value="{{ $settings['popup_title'] ?? '🎉 Ưu Đãi Đặc Biệt Hôm Nay!' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tên Nút Bấm Popup</label>
                        <input type="text" name="popup_cta_text" value="{{ $settings['popup_cta_text'] ?? 'Xem Thực Đơn Đặt Ngay →' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Nội Dung Chi Tiết Popup</label>
                        <textarea name="popup_description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 outline-none">{{ $settings['popup_description'] ?? 'Tặng ngay 01 hũ sốt đặc trưng hoặc Freeship 3km cho đơn hàng từ 100k hôm nay. Đặt ngay để nhận ưu đãi!' }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Link Chuyển Hướng Nút Bấm</label>
                        <input type="text" name="popup_cta_url" value="{{ $settings['popup_cta_url'] ?? '/menu' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Link Ảnh Banner Popup (Hoặc tải file dưới)</label>
                        <input type="text" name="popup_banner_image" value="{{ $settings['popup_banner_image'] ?? '' }}" placeholder="https://..." class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Hoặc Tải File Ảnh Banner Popup Từ Máy</label>
                        <input type="file" name="popup_banner_file" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-900 file:text-white hover:file:bg-black file:cursor-pointer">
                    </div>

                    <div class="sm:col-span-2 space-y-1 pt-2 border-t border-gray-100">
                        <label class="block text-xs font-bold text-gray-700">Thanh Thông Báo Chạy Đầu Header (Top Banner)</label>
                        <input type="text" name="top_notification" value="{{ $settings['top_notification'] ?? 'Ưu đãi hôm nay: Freeship bán kính 3km cho đơn hàng từ 100k!' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-900 outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 5: MẠNG XÃ HỘI & LIÊN HỆ ==================== -->
        <div x-show="activeTab === 'socials'" class="space-y-6" x-cloak>
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>💬</span>
                        <span>Đường Dẫn Kênh Chat & Mạng Xã Hội</span>
                    </h3>
                    <p class="text-xs text-gray-500">Cấu hình link nút chat nổi Zalo, Messenger và icon mạng xã hội ở chân trang.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Link Chat Zalo (Nút nổi)</label>
                        <input type="text" name="contact_zalo_url" value="{{ $settings['contact_zalo_url'] ?? 'https://zalo.me/0973797151' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Link Chat Facebook Messenger</label>
                        <input type="text" name="contact_messenger_url" value="{{ $settings['contact_messenger_url'] ?? 'https://m.me/luuhoang.it' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Link Fanpage Facebook</label>
                        <input type="text" name="social_facebook" value="{{ $settings['social_facebook'] ?? 'https://facebook.com' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Link Kênh TikTok</label>
                        <input type="text" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? 'https://tiktok.com' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Link Instagram</label>
                        <input type="text" name="social_instagram" value="{{ $settings['social_instagram'] ?? 'https://instagram.com' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 6: SEO & TRACKING ==================== -->
        <div x-show="activeTab === 'seo'" class="space-y-6" x-cloak>
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🔍</span>
                        <span>Cấu Hình SEO, Thẻ Meta & Mã Theo Dõi Quảng Cáo</span>
                    </h3>
                    <p class="text-xs text-gray-500">Tối ưu tìm kiếm Google và hiển thị ảnh khi chia sẻ link qua Facebook/Zalo.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tiêu Đề Trang Web (Meta Title)</label>
                        <input type="text" name="meta_title" value="{{ $settings['meta_title'] ?? 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Mô Tả Trang Web (Meta Description)</label>
                        <textarea name="meta_description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 outline-none">{{ $settings['meta_description'] ?? 'Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội. Giao nhanh 25-40 phút.' }}</textarea>
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Từ Khoá SEO (Meta Keywords)</label>
                        <input type="text" name="meta_keywords" value="{{ $settings['meta_keywords'] ?? 'gà sốt, gà rán hà nội, cơm gà sốt, gao gà rán' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Link Ảnh Chia Sẻ Facebook/Zalo (OG Image)</label>
                        <input type="text" name="og_image" value="{{ $settings['og_image'] ?? '' }}" placeholder="https://..." class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Hoặc Tải File Ảnh Chia Sẻ Từ Máy</label>
                        <input type="file" name="og_image_file" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-900 file:text-white hover:file:bg-black file:cursor-pointer">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Mã Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" placeholder="VD: G-XXXXXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Mã Facebook Pixel ID</label>
                        <input type="text" name="facebook_pixel_id" value="{{ $settings['facebook_pixel_id'] ?? '' }}" placeholder="VD: 1234567890" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 7: TELEGRAM BOT ==================== -->
        <div x-show="activeTab === 'telegram'" class="space-y-6" x-cloak>
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🔔</span>
                        <span>Cấu Hình Telegram Bot Báo Đơn Tự Động</span>
                    </h3>
                    <p class="text-xs text-gray-500">Tự động bắn thông báo chuông về điện thoại khi có khách đặt món.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Telegram Bot Token (từ @BotFather)</label>
                        <input type="text" name="telegram_bot_token" value="{{ $settings['telegram_bot_token'] ?? env('TELEGRAM_BOT_TOKEN', '') }}" placeholder="VD: 7123456789:AAHk123456789..." class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Telegram Chat ID (từ @userinfobot)</label>
                        <input type="text" name="telegram_chat_id" value="{{ $settings['telegram_chat_id'] ?? env('TELEGRAM_CHAT_ID', '') }}" placeholder="VD: 987654321" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-900 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Trạng Thái Thông Báo</label>
                        <select name="telegram_notifications_enabled" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none">
                            <option value="1" {{ ($settings['telegram_notifications_enabled'] ?? '1') == '1' ? 'selected' : '' }}>✅ Bật thông báo tức thì</option>
                            <option value="0" {{ ($settings['telegram_notifications_enabled'] ?? '1') == '0' ? 'selected' : '' }}>❌ Tắt thông báo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- STICKY ACTION BAR CỐ ĐỊNH CUỐI MÀN HÌNH -->
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-2xl py-3 px-4 sm:px-8">
            <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
                <div class="text-xs text-gray-500 font-medium">
                    Các thay đổi sẽ được cập nhật trực tiếp ra toàn bộ website & giỏ hàng của khách.
                </div>

                <button 
                    type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.01] active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                >
                    <span>💾</span>
                    <span>Lưu Cấu Hình Hệ Thống</span>
                </button>
            </div>
        </div>

    </form>

    <!-- TEST TELEGRAM CARD (Shown when on telegram tab) -->
    <div x-show="activeTab === 'telegram'" class="bg-gray-900 text-white p-6 rounded-2xl border border-gray-800 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" x-cloak>
        <div class="space-y-1">
            <h4 class="font-black text-sm text-white">Kiểm Tra Kết Nối Telegram Bot</h4>
            <p class="text-xs text-gray-400">Bấm nút để bot gửi ngay 1 tin nhắn test đến điện thoại của bạn.</p>
        </div>

        <form action="{{ route('admin.settings.test-telegram') }}" method="POST">
            @csrf
            <button 
                type="submit" 
                class="px-5 py-2.5 rounded-xl bg-[#0088cc] hover:bg-[#0077b5] text-white font-bold text-xs transition-colors flex items-center gap-2 cursor-pointer shrink-0"
            >
                <span>✈️</span>
                <span>Gửi Tin Nhắn Test Ngay</span>
            </button>
        </form>
    </div>

</div>
@endsection
