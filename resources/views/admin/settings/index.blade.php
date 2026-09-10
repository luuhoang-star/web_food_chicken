@extends('layouts.admin')

@section('title', 'Cài Đặt Vận Hành Quán & Hệ Thống')
@section('page_title', '⚙️ Cài Đặt Vận Hành Quán & Hệ Thống')

@section('content')
<div 
    class="max-w-6xl space-y-6 pb-28 relative" 
    x-data="settingsAdminData()"
>

    <!-- 1. HEADER (TITLE & ACTION BUTTONS) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>⚙️ Cài Đặt Vận Hành Quán & Hệ Thống</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">
                Cấu hình vận hành kỹ thuật: Bếp nhận đơn, Phí ship giao hàng, Ngân hàng VietQR, Báo chuông Telegram & SEO.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Nút Mở Modal Xem Trước Trực Tiếp -->
            <button 
                type="button" 
                @click="showWebPreviewModal = true"
                class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white font-black text-xs shadow-md shadow-red-500/20 flex items-center gap-2 transition-all cursor-pointer hover:scale-105 active:scale-95"
            >
                <span>👁️</span>
                <span>Xem Trước Trang Chủ</span>
            </button>

            <!-- Mở Tab Trình Duyệt Mới -->
            <a 
                href="/" 
                target="_blank" 
                class="px-3.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center gap-1.5 transition-colors"
                title="Mở website trong tab trình duyệt mới"
            >
                <span>🌐</span>
                <span class="hidden md:inline">Mở Web</span>
                <span>↗</span>
            </a>
        </div>
    </div>

    <!-- 2. TAB NAVIGATION: CÁC NHÓM VẬN HÀNH RÀNH MẠCH -->
    <div class="bg-white p-1.5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-1 overflow-x-auto text-xs font-bold scrollbar-thin">
        
        <button 
            type="button" 
            @click="setTab('store')" 
            :class="activeTab === 'store' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🏢 1. Bếp & Giờ Mở Cửa</span>
        </button>

        <button 
            type="button" 
            @click="setTab('delivery')" 
            :class="activeTab === 'delivery' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🛵 2. Giao Hàng & Phí Ship</span>
        </button>

        <button 
            type="button" 
            @click="setTab('vietqr')" 
            :class="activeTab === 'vietqr' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>💳 3. Ngân Hàng VietQR</span>
        </button>

        <button 
            type="button" 
            @click="setTab('telegram')" 
            :class="activeTab === 'telegram' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>📱 4. Báo Đơn Telegram</span>
        </button>

        <button 
            type="button" 
            @click="setTab('seo')" 
            :class="activeTab === 'seo' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0 relative"
        >
            <span>🔍 5. SEO & Google</span>
            <template x-if="seoState.isDirty">
                <span class="w-2 h-2 rounded-full bg-amber-400 absolute top-1.5 right-1.5 animate-pulse"></span>
            </template>
        </button>

    </div>

    <!-- TAB 1: 🏢 BẾP & GIỜ MỞ CỬA -->
    <div x-show="activeTab === 'store'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-5">
            
            <!-- Trạng thái mở quán cấp tốc -->
            <div class="p-4 rounded-2xl border flex items-center justify-between flex-wrap gap-4 {{ ($settings['store_open_status'] ?? 'open') === 'open' ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200' }}">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full {{ ($settings['store_open_status'] ?? 'open') === 'open' ? 'bg-emerald-600 animate-pulse' : 'bg-rose-600' }}"></span>
                        <h4 class="font-black text-sm {{ ($settings['store_open_status'] ?? 'open') === 'open' ? 'text-emerald-900' : 'text-rose-900' }}">
                            {{ ($settings['store_open_status'] ?? 'open') === 'open' ? 'BẾP ĐANG MỞ - ĐANG NHẬN ĐƠN HÀNG' : 'BẾP ĐANG TẠM DỪNG NHẬN ĐƠN' }}
                        </h4>
                    </div>
                    <p class="text-xs {{ ($settings['store_open_status'] ?? 'open') === 'open' ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ ($settings['store_open_status'] ?? 'open') === 'open' ? 'Khách hàng có thể chọn món và đặt giao hàng bình thường.' : 'Website tạm thời chặn khách đặt hàng (dùng khi quán quá tải hoặc hết món).' }}
                    </p>
                </div>

                <form action="{{ route('admin.settings.toggle-store-status') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button 
                        type="submit" 
                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider shadow-sm transition-all cursor-pointer {{ ($settings['store_open_status'] ?? 'open') === 'open' ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}"
                    >
                        {{ ($settings['store_open_status'] ?? 'open') === 'open' ? '⏸️ Tạm Dừng Nhận Đơn' : '▶️ Mở Bếp Nhận Đơn Ngay' }}
                    </button>
                </form>
            </div>

            <!-- LIVE PREVIEW BANNER MÔ PHỎNG TRÊN WEBSITE (CHUẨN TÔNG MÀU TRANG CHỦ) -->
            <div class="bg-[#FFFDF8] rounded-2xl p-4 sm:p-5 border-2 border-orange-200/70 space-y-3 shadow-inner">
                <div class="flex items-center justify-between gap-2 border-b border-orange-100 pb-2.5 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>👁️</span>
                            <span>Mô Phỏng Thanh Thông Báo Đầu Website (Top Banner):</span>
                        </span>
                    </div>
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true"
                        class="text-[11px] font-bold text-red-600 hover:text-red-700 underline cursor-pointer"
                    >
                        Xem trên toàn trang web &rarr;
                    </button>
                </div>

                <!-- Preview Banner khi Mở / Tạm Dừng -->
                @if(($settings['store_open_status'] ?? 'open') === 'paused')
                    <div class="bg-rose-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl text-center tracking-wide flex items-center justify-center gap-2 shadow-md">
                        <span class="inline-block animate-pulse text-base">⚠️</span>
                        <span><strong>BẾP ĐANG TẠM DỪNG NHẬN ĐƠN ÍT PHÚT:</strong> Bếp đang bận xử lý giao hàng / chuẩn bị gà nóng. Quý khách vẫn có thể xem thực đơn hoặc gọi Hotline: <strong>{{ $settings['hotline'] ?? '0988.868.GAO' }}</strong></span>
                    </div>
                @else
                    <div class="bg-gradient-to-r from-red-600 to-amber-600 text-white text-xs font-semibold py-2 px-4 rounded-xl text-center tracking-wide flex items-center justify-center gap-2 shadow-md">
                        <span class="inline-block animate-pulse">🔥</span>
                        <span>{{ $settings['top_notification'] ?? 'Ưu đãi hôm nay: Freeship bán kính 3km cho đơn hàng từ 100k!' }}</span>
                        <span class="hidden md:inline">• Hotline: <strong>{{ $settings['hotline'] ?? '0988.868.GAO' }}</strong></span>
                    </div>
                @endif

                <!-- Preview Lời nhắc giờ cao điểm -->
                <div class="bg-amber-50 rounded-xl p-3 border border-amber-200/80 flex items-start gap-2.5 text-xs text-amber-950">
                    <span class="text-base">📢</span>
                    <div>
                        <span class="font-bold text-amber-900">Lời nhắc / Ghi chú giờ cao điểm hiển thị:</span>
                        <p class="text-amber-800 pt-0.5 font-medium" x-text="storeState.rushNote || '(Chưa nhập ghi chú)'"></p>
                    </div>
                </div>
            </div>

            <!-- Form Cài đặt giờ mở cửa -->
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4 pt-1">
                @csrf
                <input type="hidden" name="_redirect_to" value="{{ route('admin.settings.index', ['tab' => 'store']) }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Giờ Bắt Đầu Nhận Đơn (Buổi Sáng)</label>
                        <input 
                            type="time" 
                            name="kitchen_open_time" 
                            x-model="storeState.openTime"
                            value="{{ $settings['kitchen_open_time'] ?? '09:30' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Giờ Ngừng Nhận Đơn (Buổi Tối)</label>
                        <input 
                            type="time" 
                            name="kitchen_close_time" 
                            x-model="storeState.closeTime"
                            value="{{ $settings['kitchen_close_time'] ?? '22:30' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="block font-bold text-gray-700">Ghi chú giờ cao điểm / Lời nhắc khách hàng</label>
                        <input 
                            type="text" 
                            name="rush_hour_note" 
                            x-model="storeState.rushNote"
                            value="{{ $settings['rush_hour_note'] ?? 'Giờ trưa 11:30 - 13:00 quán có thể giao chậm hơn 10 phút, mong quý khách thông cảm!' }}"
                            placeholder="Thông báo cho khách khi quán đông..." 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100 flex-wrap gap-2">
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true"
                        class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>👁️ Xem Thử Website</span>
                    </button>

                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cài Đặt Giờ Hoạt Động
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- TAB 2: 🛵 GIAO HÀNG & PHÍ SHIP -->
    <div x-show="activeTab === 'delivery'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.settings.index', ['tab' => 'delivery']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-5">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🛵</span>
                            <span>Cấu Hình Bán Kính Giao Hàng & Phí Ship</span>
                        </h3>
                        <p class="text-xs text-gray-400">Tự động tính phí vận chuyển khi khách chọn địa chỉ hoặc khoảng cách km</p>
                    </div>
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true"
                        class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold border border-amber-200 transition-colors cursor-pointer flex items-center gap-1"
                    >
                        <span>👁️ Xem Thử Trang Chủ</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Khoảng cách cơ bản (km)</label>
                        <input 
                            type="number" 
                            name="shipping_base_distance" 
                            x-model.number="deliveryState.baseDistance"
                            value="{{ $settings['shipping_base_distance'] ?? '3' }}"
                            step="0.5" 
                            min="0"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Phí ship cơ bản trong khoảng cách trên (VNĐ)</label>
                        <input 
                            type="number" 
                            name="shipping_base_fee" 
                            x-model.number="deliveryState.baseFee"
                            value="{{ $settings['shipping_base_fee'] ?? '15000' }}"
                            step="1000" 
                            min="0"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-red-600 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Phí cộng thêm mỗi km tiếp theo (VNĐ/km)</label>
                        <input 
                            type="number" 
                            name="shipping_per_km_fee" 
                            x-model.number="deliveryState.perKmFee"
                            value="{{ $settings['shipping_per_km_fee'] ?? '5000' }}"
                            step="1000" 
                            min="0"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Bán kính giao hàng tối đa (km)</label>
                        <input 
                            type="number" 
                            name="shipping_max_distance" 
                            x-model.number="deliveryState.maxDistance"
                            value="{{ $settings['shipping_max_distance'] ?? '10' }}"
                            step="1" 
                            min="1"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Giá trị đơn hàng tối thiểu được FREESHIP (VNĐ)</label>
                        <input 
                            type="number" 
                            name="freeship_threshold" 
                            x-model.number="deliveryState.freeshipThreshold"
                            value="{{ $settings['freeship_threshold'] ?? '150000' }}"
                            step="5000" 
                            min="0"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-emerald-600 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Thời gian giao hàng ước tính hiển thị</label>
                        <input 
                            type="text" 
                            name="delivery_time_estimate" 
                            x-model="deliveryState.estimateTime"
                            value="{{ $settings['delivery_time_estimate'] ?? '25 - 40 phút' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <!-- BỘ CÔNG CỤ LIVE PREVIEW MÔ PHỎNG TÍNH PHÍ SHIP -->
                <div class="bg-gradient-to-br from-gray-900 to-stone-900 rounded-2xl p-4 sm:p-5 text-white space-y-4 shadow-lg border border-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-700/80 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span>👁️</span>
                                <span>Mô Phỏng Trực Quan Bộ Tính Phí Ship Trực Tuyến:</span>
                            </span>
                        </div>
                        <span class="text-[11px] text-gray-400 font-medium hidden sm:inline">Thay đổi các giá trị bên trên để xem kết quả tính tự động</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        <!-- Controls Thử Nghiệm -->
                        <div class="md:col-span-6 space-y-3 text-xs bg-gray-800/80 p-3.5 rounded-xl border border-gray-700">
                            <div class="space-y-1">
                                <div class="flex justify-between font-bold">
                                    <span class="text-gray-300">Khoảng cách khách ở:</span>
                                    <span class="text-amber-400 font-mono font-black" x-text="deliveryState.simDistance + ' km'"></span>
                                </div>
                                <input 
                                    type="range" 
                                    min="0.5" 
                                    :max="deliveryState.maxDistance || 15" 
                                    step="0.5" 
                                    x-model="deliveryState.simDistance"
                                    class="w-full accent-amber-500 cursor-pointer"
                                >
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between font-bold">
                                    <span class="text-gray-300">Tổng tiền món khách chọn:</span>
                                    <span class="text-emerald-400 font-mono font-black" x-text="deliveryState.formatMoney(deliveryState.simSubtotal)"></span>
                                </div>
                                <input 
                                    type="range" 
                                    min="30000" 
                                    max="300000" 
                                    step="10000" 
                                    x-model="deliveryState.simSubtotal"
                                    class="w-full accent-emerald-500 cursor-pointer"
                                >
                            </div>
                        </div>

                        <!-- Kết Quả Hiển Thị Cho Khách -->
                        <div class="md:col-span-6 bg-gray-950 p-4 rounded-xl border border-gray-800 space-y-2 text-center">
                            <span class="text-[11px] text-gray-400 uppercase font-black tracking-wider block">Phí Vận Chuyển Khách Phải Trả:</span>
                            
                            <div class="py-1">
                                <span 
                                    class="text-2xl sm:text-3xl font-black font-mono"
                                    :class="deliveryState.calculatedFee === 0 ? 'text-emerald-400' : 'text-red-400'"
                                    x-text="deliveryState.calculatedFee === 0 ? '🎉 MIỄN PHÍ SHIP (0đ)' : deliveryState.formatMoney(deliveryState.calculatedFee)"
                                ></span>
                            </div>

                            <div class="text-[11px] text-gray-400 font-medium">
                                <template x-if="deliveryState.calculatedFee === 0">
                                    <span class="text-emerald-300 font-bold">Đơn hàng đạt mức Freeship &ge; <span x-text="deliveryState.formatMoney(deliveryState.freeshipThreshold)"></span>!</span>
                                </template>
                                <template x-if="deliveryState.calculatedFee > 0">
                                    <span>
                                        Gồm <span x-text="deliveryState.formatMoney(deliveryState.baseFee)"></span> (cho <span x-text="deliveryState.baseDistance"></span>km đầu)
                                        <template x-if="deliveryState.simDistance > deliveryState.baseDistance">
                                            <span> + phụ thu <span x-text="deliveryState.formatMoney(deliveryState.calculatedFee - deliveryState.baseFee)"></span></span>
                                        </template>
                                    </span>
                                </template>
                            </div>
                            <div class="text-[10px] text-amber-300 font-bold pt-1">
                                ⏱️ Thời gian ước tính: <span x-text="deliveryState.estimateTime"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Box Giải Thích Trực Quan Công Thức Tính Tiền -->
                <div class="bg-amber-50/70 rounded-2xl p-4 border border-amber-200/80 space-y-2 text-xs text-amber-950">
                    <div class="font-black text-amber-900 flex items-center gap-1.5 text-sm">
                        <span>💡</span>
                        <span>Nguyên tắc hệ thống tính tiền ship & Freeship tự động cho khách:</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1 text-xs">
                        <div class="bg-white/80 rounded-xl p-3 border border-amber-200/60 space-y-1">
                            <span class="font-extrabold text-emerald-700 flex items-center gap-1">
                                <span>🎉</span> 1. Đơn đủ điều kiện FREESHIP (0đ):
                            </span>
                            <p class="text-gray-600 leading-relaxed">
                                Khách đặt món có <strong>Tổng tiền &ge; Giá trị tối thiểu Freeship</strong> &rarr; <span class="text-emerald-700 font-bold">Phí ship = 0đ</span> (Freeship hoàn toàn).
                            </p>
                        </div>
                        <div class="bg-white/80 rounded-xl p-3 border border-amber-200/60 space-y-1">
                            <span class="font-extrabold text-red-700 flex items-center gap-1">
                                <span>🛵</span> 2. Đơn chưa đủ Freeship:
                            </span>
                            <p class="text-gray-600 leading-relaxed">
                                &bull; Trong <strong>Khoảng cách cơ bản</strong>: Phí cố định là <strong>Phí ship cơ bản</strong>.<br>
                                &bull; Vượt quá: Mỗi km thêm cộng thêm <strong>Phí mỗi km tiếp theo</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100 flex-wrap gap-2">
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true"
                        class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>👁️ Xem Thử Website</span>
                    </button>

                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cấu Hình Giao Hàng
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 3: 💳 NGÂN HÀNG VIETQR -->
    <div x-show="activeTab === 'vietqr'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.settings.index', ['tab' => 'vietqr']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>💳</span>
                            <span>Cấu Hình Tài Khoản Ngân Hàng VietQR (Tự Động Tạo Mã Chuyển Khoản)</span>
                        </h3>
                        <p class="text-xs text-gray-400">Khách quét mã chuyển khoản sẽ tự điền sẵn Số tiền & Mã đơn hàng</p>
                    </div>

                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true"
                        class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 text-xs font-bold border border-blue-200 transition-colors cursor-pointer flex items-center gap-1"
                    >
                        <span>👁️ Xem Thử Trang Chủ</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    <div class="md:col-span-7 space-y-3 text-xs">
                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Ngân Hàng Nhận Tiền</label>
                            <input type="hidden" name="bank_name" :value="{
                                'MB': 'MB Bank (Quân Đội)',
                                'VCB': 'Vietcombank (Ngoại Thương)',
                                'TCB': 'Techcombank (Kỹ Thương)',
                                'VPB': 'VPBank (Việt Nam Thịnh Vượng)',
                                'TPB': 'TPBank (Tiên Phong)',
                                'ACB': 'ACB (Á Châu)',
                                'BIDV': 'BIDV (Đầu Tư & Phát Triển)',
                                'VBA': 'Agribank (Nông Nghiệp)',
                                'CTG': 'VietinBank (Công Thương)',
                                'MSB': 'MSB (Hàng Hải)'
                            }[bankState.bankCode] || 'Vietcombank'}">
                            <select 
                                name="bank_code" 
                                x-model="bankState.bankCode"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none cursor-pointer"
                            >
                                <option value="MB">MB Bank (Ngân Hàng Quân Đội)</option>
                                <option value="VCB">Vietcombank (Ngoại Thương)</option>
                                <option value="TCB">Techcombank (Kỹ Thương)</option>
                                <option value="VPB">VPBank (Việt Nam Thịnh Vượng)</option>
                                <option value="TPB">TPBank (Tiên Phong)</option>
                                <option value="ACB">ACB (Á Châu)</option>
                                <option value="BIDV">BIDV (Đầu Tư & Phát Triển)</option>
                                <option value="VBA">Agribank (Nông Nghiệp)</option>
                                <option value="CTG">VietinBank (Công Thương)</option>
                                <option value="MSB">MSB (Hàng Hải)</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Số Tài Khoản Ngân Hàng</label>
                            <input 
                                type="text" 
                                name="bank_account_number" 
                                x-model="bankState.accountNumber"
                                placeholder="VD: 0988888888" 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono font-black text-sm text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Tên Chủ Tài Khoản (Viết hoa không dấu)</label>
                            <input 
                                type="text" 
                                name="bank_account_holder" 
                                x-model="bankState.accountHolder"
                                placeholder="VD: NGUYEN VAN A" 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-xs text-gray-900 uppercase focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Cú Pháp / Tiền Tố Chuyển Khoản (Đổi theo ý muốn)</label>
                            <input 
                                type="text" 
                                name="bank_transfer_prefix" 
                                x-model="bankState.prefix"
                                placeholder="VD: HUBBY, GAO, CHICKEN..." 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-xs text-gray-900 uppercase focus:bg-white focus:border-red-500 outline-none"
                            >
                            <p class="text-[11px] text-gray-400">Nội dung khi khách chuyển khoản sẽ tự động ghép: <strong>[TIỀN TỐ] [SĐT KHÁCH]</strong></p>
                        </div>
                    </div>

                    <!-- Live VietQR Preview Box -->
                    <div class="md:col-span-5 bg-gradient-to-br from-blue-900 to-indigo-950 p-5 rounded-2xl text-white space-y-3 text-center shadow-md">
                        <span class="text-[10px] font-black uppercase text-blue-300 tracking-wider block">👁️ Xem Trước Thông Tin VietQR:</span>
                        
                        <div class="bg-white p-2.5 rounded-2xl inline-block shadow-lg">
                            <img 
                                :src="`https://img.vietqr.io/image/${bankState.bankCode}-${bankState.accountNumber}-compact2.png?amount=100000&addInfo=${encodeURIComponent((bankState.prefix || 'HUBBY').trim())}0988888888&accountName=${encodeURIComponent(bankState.accountHolder)}`"
                                alt="Mã QR Chuyển Khoản Mẫu"
                                class="w-44 sm:w-48 h-auto mx-auto object-contain rounded-xl"
                            >
                        </div>

                        <div class="space-y-0.5 text-xs font-bold">
                            <div class="font-black text-amber-300" x-text="bankState.bankCode"></div>
                            <div class="font-mono text-sm tracking-wider" x-text="bankState.accountNumber"></div>
                            <div class="text-[11px] text-gray-300 uppercase" x-text="bankState.accountHolder"></div>
                            <div class="text-[11px] text-emerald-300 font-mono pt-1" x-text="'Nội dung mẫu: ' + (bankState.prefix || 'HUBBY') + ' 0988888888'"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100 flex-wrap gap-2">
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true"
                        class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>👁️ Xem Thử Website</span>
                    </button>

                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cấu Hình VietQR
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 4: 📱 THÔNG BÁO TELEGRAM -->
    <div x-show="activeTab === 'telegram'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>📱</span>
                        <span>Thông Báo Đơn Hàng Mới Về Telegram</span>
                    </h3>
                    <p class="text-xs text-gray-400">Báo chuông tức thì về điện thoại khi có khách đặt món trên website</p>
                </div>

                <form action="{{ route('admin.settings.test-telegram') }}" method="POST">
                    @csrf
                    <button 
                        type="submit" 
                        class="px-4 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-black transition-colors cursor-pointer flex items-center gap-1.5"
                    >
                        <span>🔔</span>
                        <span>Gửi Tin Nhắn Thử Nghiệm</span>
                    </button>
                </form>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_redirect_to" value="{{ route('admin.settings.index', ['tab' => 'telegram']) }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Telegram Bot Token</label>
                        <input 
                            type="password" 
                            name="telegram_bot_token" 
                            value="{{ $settings['telegram_bot_token'] ?? '' }}"
                            placeholder="VD: 123456789:ABCdefGHIjklMNOpqrs..."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Telegram Chat ID (ID Nhóm / Cá nhân nhận tin)</label>
                        <input 
                            type="text" 
                            name="telegram_chat_id" 
                            value="{{ $settings['telegram_chat_id'] ?? '' }}"
                            placeholder="VD: -100123456789 hoặc 987654321"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="block font-bold text-gray-700">Trạng Thái Thông Báo</label>
                        <select 
                            name="telegram_notify_new_order" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none cursor-pointer"
                        >
                            <option value="1" {{ ($settings['telegram_notify_new_order'] ?? '1') == '1' ? 'selected' : '' }}>🟢 BẬT thông báo khi có đơn hàng mới</option>
                            <option value="0" {{ ($settings['telegram_notify_new_order'] ?? '1') == '0' ? 'selected' : '' }}>⚫ TẮT thông báo</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100 flex-wrap gap-2">
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true"
                        class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>👁️ Xem Thử Website</span>
                    </button>

                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cấu Hình Telegram
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 5: 🔍 SEO & GOOGLE (TỐI ƯU TOÀN DIỆN UI/UX) -->
    <div x-show="activeTab === 'seo'" class="space-y-5" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5" id="seoSettingsForm">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.settings.index', ['tab' => 'seo']) }}">
            
            <!-- Hidden inputs để lưu trạng thái xóa/giữ nguyên ảnh khi submit -->
            <input type="hidden" name="og_image" :value="seoState.ogDeleted ? '' : seoState.ogRawValue">
            <input type="hidden" name="favicon_url" :value="seoState.favDeleted ? '' : seoState.favRawValue">

            <!-- Card chính cấu hình SEO -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-4 sm:p-6 space-y-6">
                
                <!-- Header của Card -->
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between flex-wrap gap-3">
                    <div class="space-y-0.5">
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🔍</span>
                            <span>Cấu Hình SEO Google & Chia Sẻ Mạng Xã Hội (Facebook, Zalo)</span>
                        </h3>
                        <p class="text-xs text-gray-400">Tối ưu thẻ tìm kiếm Google, xem trước thẻ mạng xã hội và quản lý ảnh đại diện / icon website</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Trạng thái thay đổi -->
                        <div class="text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1.5 transition-all"
                            :class="seoState.isDirty ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'">
                            <span class="w-2 h-2 rounded-full" :class="seoState.isDirty ? 'bg-amber-500 animate-ping' : 'bg-emerald-500'"></span>
                            <span x-text="seoState.isDirty ? 'Có thay đổi chưa lưu' : 'Đã đồng bộ'"></span>
                        </div>

                        <button 
                            type="button" 
                            @click="showWebPreviewModal = true"
                            class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 text-xs font-bold border border-blue-200 transition-colors cursor-pointer flex items-center gap-1"
                        >
                            <span>👁️ Xem Toàn Trang</span>
                        </button>
                    </div>
                </div>

                <!-- BỐ CỤC 2 CỘT CÂN ĐỐI: FORM CHỈNH SỬA & LIVE PREVIEW GỌN GÀNG -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- CỘT TRÁI (7/12): CÁC Ô NHẬP LIỆU & QUẢN LÝ ẢNH -->
                    <div class="lg:col-span-7 space-y-5 text-xs">
                        
                        <!-- 1. Meta Title -->
                        <div class="space-y-1.5 bg-gray-50/70 p-3.5 rounded-2xl border border-gray-200/80">
                            <div class="flex items-center justify-between">
                                <label class="font-black text-gray-800 flex items-center gap-1.5">
                                    <span>🏷️</span>
                                    <span>Tiêu Đề Trang Web (Meta Title)</span>
                                </label>
                                <span class="text-[11px] font-mono font-bold" :class="seoState.titleStatus.color">
                                    <span x-text="seoState.titleLength"></span>/60 ký tự
                                </span>
                            </div>

                            <input 
                                type="text" 
                                name="meta_title" 
                                x-model="seoState.metaTitle"
                                placeholder="VD: GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị" 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-bold text-gray-900 focus:border-red-500 outline-none shadow-2xs transition-colors"
                            >

                            <!-- Thanh tiến trình độ dài & Thông báo trạng thái -->
                            <div class="space-y-1 pt-1">
                                <div class="w-full bg-gray-200 h-1 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-300" :class="seoState.titleStatus.barColor" :style="`width: ${seoState.titleStatus.pct}%`"></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px]">
                                    <span :class="seoState.titleStatus.color" x-text="seoState.titleStatus.label"></span>
                                    <span class="text-gray-400">Khuyên dùng: 40 - 60 ký tự</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Meta Description -->
                        <div class="space-y-1.5 bg-gray-50/70 p-3.5 rounded-2xl border border-gray-200/80">
                            <div class="flex items-center justify-between">
                                <label class="font-black text-gray-800 flex items-center gap-1.5">
                                    <span>📝</span>
                                    <span>Mô Tả Tìm Kiếm (Meta Description)</span>
                                </label>
                                <span class="text-[11px] font-mono font-bold" :class="seoState.descStatus.color">
                                    <span x-text="seoState.descLength"></span>/160 ký tự
                                </span>
                            </div>

                            <textarea 
                                name="meta_description" 
                                x-model="seoState.metaDesc"
                                rows="3" 
                                placeholder="VD: Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội. Giao nhanh 25-40 phút." 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-medium text-gray-800 focus:border-red-500 outline-none shadow-2xs transition-colors resize-none leading-relaxed"
                            ></textarea>

                            <!-- Thanh tiến trình độ dài & Thông báo trạng thái -->
                            <div class="space-y-1 pt-0.5">
                                <div class="w-full bg-gray-200 h-1 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-300" :class="seoState.descStatus.barColor" :style="`width: ${seoState.descStatus.pct}%`"></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px]">
                                    <span :class="seoState.descStatus.color" x-text="seoState.descStatus.label"></span>
                                    <span class="text-gray-400">Khuyên dùng: 120 - 160 ký tự</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Quản Lý Ảnh Đại Diện OG Image -->
                        <div class="space-y-3 bg-amber-50/40 p-3.5 sm:p-4 rounded-2xl border border-amber-200/80">
                            <div class="flex items-center justify-between">
                                <label class="font-black text-gray-900 text-xs flex items-center gap-1.5">
                                    <span>🖼️</span>
                                    <span>Ảnh Đại Diện Chia Sẻ Link (OG Image)</span>
                                </label>
                                <span class="text-[10px] text-amber-800 font-bold bg-amber-100/80 px-2 py-0.5 rounded-md font-mono">1200 x 630 px</span>
                            </div>

                            <!-- Thẻ Thumbnail Preview & Action Buttons -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <!-- Khung Thumbnail -->
                                <div class="w-32 h-20 rounded-xl bg-gray-900 border border-gray-300 overflow-hidden relative shadow-xs shrink-0 flex items-center justify-center">
                                    <template x-if="seoState.ogFilePreview || (!seoState.ogDeleted && seoState.ogImageUrl)">
                                        <img 
                                            :src="seoState.ogFilePreview || seoState.ogImageUrl" 
                                            alt="OG Thumbnail" 
                                            class="w-full h-full object-cover"
                                        >
                                    </template>
                                    <template x-if="seoState.ogDeleted || (!seoState.ogFilePreview && !seoState.ogImageUrl)">
                                        <div class="text-[10px] text-gray-400 font-bold text-center p-1 flex flex-col items-center">
                                            <span>🖼️</span>
                                            <span>Chưa có ảnh</span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Controls & Validation Status -->
                                <div class="space-y-2 flex-1 w-full">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <!-- Nút Tải/Thay Ảnh -->
                                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-gray-300 hover:border-red-500 hover:bg-red-50/50 text-gray-800 font-bold text-xs cursor-pointer shadow-2xs transition-all">
                                            <span>📁</span>
                                            <span x-text="seoState.ogFilePreview || (!seoState.ogDeleted && seoState.ogImageUrl) ? 'Thay Ảnh Khác...' : 'Tải Ảnh Lên...'"></span>
                                            <input 
                                                type="file" 
                                                id="og_image_file_input"
                                                name="og_image_file" 
                                                @change="seoState.handleOgFile"
                                                accept="image/png,image/jpeg,image/webp,image/gif"
                                                class="hidden"
                                            >
                                        </label>

                                        <!-- Nút Hủy File Vừa Chọn -->
                                        <template x-if="seoState.ogFilePreview">
                                            <button 
                                                type="button" 
                                                @click="seoState.cancelOgUpload()"
                                                class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold transition-colors cursor-pointer"
                                                title="Hủy file mới chọn, quay về ảnh cũ"
                                            >
                                                ↩️ Hủy chọn
                                            </button>
                                        </template>

                                        <!-- Nút Xóa Ảnh Đang Có -->
                                        <template x-if="!seoState.ogDeleted && (seoState.ogImageUrl || seoState.ogFilePreview)">
                                            <button 
                                                type="button" 
                                                @click="seoState.deleteOgImage()"
                                                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors cursor-pointer"
                                                title="Xóa ảnh đại diện hiện tại"
                                            >
                                                🗑️ Xóa ảnh
                                            </button>
                                        </template>

                                        <!-- Nút Khôi phục nếu vừa ấn xóa -->
                                        <template x-if="seoState.ogDeleted && seoState.initial.ogImageUrl">
                                            <button 
                                                type="button" 
                                                @click="seoState.restoreOgImage()"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition-colors cursor-pointer"
                                            >
                                                ♻️ Khôi phục ảnh cũ
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Thông Báo Kiểm Tra Kích Thước / Định Dạng -->
                                    <div>
                                        <template x-if="seoState.ogValidation.status !== 'none'">
                                            <div 
                                                class="p-2 rounded-lg text-[11px] font-medium leading-tight"
                                                :class="{
                                                    'bg-emerald-50 text-emerald-800 border border-emerald-200': seoState.ogValidation.type === 'success',
                                                    'bg-amber-50 text-amber-900 border border-amber-200': seoState.ogValidation.type === 'warning',
                                                    'bg-rose-50 text-rose-900 border border-rose-200': seoState.ogValidation.type === 'error'
                                                }"
                                                x-text="seoState.ogValidation.message"
                                            ></div>
                                        </template>
                                        <template x-if="seoState.ogValidation.status === 'none' && !seoState.ogDeleted && seoState.ogImageUrl">
                                            <p class="text-[10px] text-gray-500">
                                                ✅ Đang sử dụng ảnh đại diện hiện có trên hệ thống. Khuyên dùng file tỉ lệ 1.91:1 (1200x630px).
                                            </p>
                                        </template>
                                        <template x-if="seoState.ogDeleted">
                                            <p class="text-[10px] text-rose-600 font-bold">
                                                ⚠️ Ảnh đại diện sẽ bị xóa sau khi bạn ấn Lưu Cấu Hình.
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Quản Lý Icon Tab (Favicon) -->
                        <div class="space-y-3 bg-amber-50/40 p-3.5 sm:p-4 rounded-2xl border border-amber-200/80">
                            <div class="flex items-center justify-between">
                                <label class="font-black text-gray-900 text-xs flex items-center gap-1.5">
                                    <span>⭐</span>
                                    <span>Icon Tab Trình Duyệt (Favicon)</span>
                                </label>
                                <span class="text-[10px] text-amber-800 font-bold bg-amber-100/80 px-2 py-0.5 rounded-md font-mono">32x32 / 64x64 px</span>
                            </div>

                            <!-- Thẻ Thumbnail Favicon & Controls -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <!-- Khung Favicon Thumbnail -->
                                <div class="w-14 h-14 rounded-xl bg-gray-100 border border-gray-300 overflow-hidden relative shadow-xs shrink-0 flex items-center justify-center p-2">
                                    <template x-if="seoState.favFilePreview || (!seoState.favDeleted && seoState.faviconUrl)">
                                        <img 
                                            :src="seoState.favFilePreview || seoState.faviconUrl" 
                                            alt="Favicon Thumbnail" 
                                            class="w-full h-full object-contain"
                                        >
                                    </template>
                                    <template x-if="seoState.favDeleted || (!seoState.favFilePreview && !seoState.faviconUrl)">
                                        <span class="text-[10px] text-gray-400 font-bold">Chưa có</span>
                                    </template>
                                </div>

                                <!-- Controls Favicon -->
                                <div class="space-y-2 flex-1 w-full">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <!-- Nút Tải Favicon -->
                                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-gray-300 hover:border-red-500 hover:bg-red-50/50 text-gray-800 font-bold text-xs cursor-pointer shadow-2xs transition-all">
                                            <span>📁</span>
                                            <span x-text="seoState.favFilePreview || (!seoState.favDeleted && seoState.faviconUrl) ? 'Thay Icon Khác...' : 'Tải Favicon Lên...'"></span>
                                            <input 
                                                type="file" 
                                                id="favicon_file_input"
                                                name="favicon_file" 
                                                @change="seoState.handleFaviconFile"
                                                accept="image/png,image/x-icon,image/svg+xml,image/jpeg"
                                                class="hidden"
                                            >
                                        </label>

                                        <!-- Nút Hủy File Vừa Chọn -->
                                        <template x-if="seoState.favFilePreview">
                                            <button 
                                                type="button" 
                                                @click="seoState.cancelFavUpload()"
                                                class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold transition-colors cursor-pointer"
                                            >
                                                ↩️ Hủy chọn
                                            </button>
                                        </template>

                                        <!-- Nút Xóa Favicon -->
                                        <template x-if="!seoState.favDeleted && (seoState.faviconUrl || seoState.favFilePreview)">
                                            <button 
                                                type="button" 
                                                @click="seoState.deleteFavicon()"
                                                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors cursor-pointer"
                                            >
                                                🗑️ Xóa Icon
                                            </button>
                                        </template>

                                        <!-- Nút Khôi phục Favicon -->
                                        <template x-if="seoState.favDeleted && seoState.initial.faviconUrl">
                                            <button 
                                                type="button" 
                                                @click="seoState.restoreFavicon()"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition-colors cursor-pointer"
                                            >
                                                ♻️ Khôi phục
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Thông Báo Favicon -->
                                    <div>
                                        <template x-if="seoState.favValidation.status !== 'none'">
                                            <div 
                                                class="p-2 rounded-lg text-[11px] font-medium leading-tight"
                                                :class="{
                                                    'bg-emerald-50 text-emerald-800 border border-emerald-200': seoState.favValidation.type === 'success',
                                                    'bg-rose-50 text-rose-900 border border-rose-200': seoState.favValidation.type === 'error'
                                                }"
                                                x-text="seoState.favValidation.message"
                                            ></div>
                                        </template>
                                        <template x-if="seoState.favValidation.status === 'none' && !seoState.favDeleted && seoState.faviconUrl">
                                            <p class="text-[10px] text-gray-500">
                                                ✅ Đang sử dụng favicon hiện có. Hỗ trợ file PNG, ICO, SVG (tối đa 2MB).
                                            </p>
                                        </template>
                                        <template x-if="seoState.favDeleted">
                                            <p class="text-[10px] text-rose-600 font-bold">
                                                ⚠️ Favicon sẽ bị xóa sau khi bạn ấn Lưu Cấu Hình.
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- CỘT PHẢI (5/12): LIVE PREVIEW HUB GỌN GÀNG, ĐỒNG BỘ REALTIME -->
                    <div class="lg:col-span-5 space-y-3 sticky top-6">
                        
                        <!-- Header Preview Tabs -->
                        <div class="bg-gray-900 text-white p-3.5 rounded-2xl space-y-3 shadow-md border border-gray-800">
                            
                            <div class="flex items-center justify-between border-b border-gray-800 pb-2.5">
                                <span class="text-xs font-black uppercase text-amber-400 tracking-wider flex items-center gap-1.5">
                                    <span>👁️</span>
                                    <span>Xem Trước Realtime</span>
                                </span>
                                <span class="text-[10px] text-gray-400 font-mono">Đồng bộ tự động</span>
                            </div>

                            <!-- Selector: Chọn Kênh Preview -->
                            <div class="grid grid-cols-3 gap-1 bg-gray-950 p-1 rounded-xl border border-gray-800 text-[11px] font-bold">
                                <button 
                                    type="button" 
                                    @click="seoState.previewTab = 'social'"
                                    :class="seoState.previewTab === 'social' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-400 hover:text-gray-200'"
                                    class="py-1.5 px-2 rounded-lg transition-all text-center cursor-pointer truncate"
                                >
                                    📱 Mạng Xã Hội
                                </button>
                                <button 
                                    type="button" 
                                    @click="seoState.previewTab = 'google'"
                                    :class="seoState.previewTab === 'google' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-400 hover:text-gray-200'"
                                    class="py-1.5 px-2 rounded-lg transition-all text-center cursor-pointer truncate"
                                >
                                    🌐 Google
                                </button>
                                <button 
                                    type="button" 
                                    @click="seoState.previewTab = 'browser'"
                                    :class="seoState.previewTab === 'browser' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-400 hover:text-gray-200'"
                                    class="py-1.5 px-2 rounded-lg transition-all text-center cursor-pointer truncate"
                                >
                                    💻 Tab Web
                                </button>
                            </div>

                            <!-- 1. PREVIEW: THẺ CHIA SẺ MẠNG XÃ HỘI (Facebook, Zalo, Telegram) -->
                            <div x-show="seoState.previewTab === 'social'" class="space-y-2">
                                <div class="text-[10px] text-gray-400 font-medium flex justify-between">
                                    <span>Hiển thị khi gửi link qua Zalo / Facebook:</span>
                                    <span class="text-amber-400">Tỉ lệ 1.91:1</span>
                                </div>

                                <div class="border border-gray-700 bg-white rounded-xl overflow-hidden shadow-lg">
                                    <!-- Ảnh OG Preview -->
                                    <div class="w-full aspect-[1.91/1] bg-gray-950 relative overflow-hidden flex items-center justify-center">
                                        <template x-if="seoState.ogFilePreview || (!seoState.ogDeleted && seoState.ogImageUrl)">
                                            <img 
                                                :src="seoState.ogFilePreview || seoState.ogImageUrl" 
                                                alt="Social Card Preview" 
                                                class="w-full h-full object-cover transition-all duration-300"
                                            >
                                        </template>
                                        <template x-if="seoState.ogDeleted || (!seoState.ogFilePreview && !seoState.ogImageUrl)">
                                            <div class="text-xs text-gray-400 font-bold flex flex-col items-center gap-1 p-4 text-center">
                                                <span class="text-2xl">🖼️</span>
                                                <span>Chưa có ảnh đại diện OG Image</span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Text Bên Dưới Ảnh -->
                                    <div class="p-3 bg-gray-50 border-t border-gray-100 space-y-1">
                                        <div class="flex items-center gap-1.5 text-[9px] uppercase font-mono font-bold text-gray-500">
                                            <template x-if="seoState.favFilePreview || (!seoState.favDeleted && seoState.faviconUrl)">
                                                <img :src="seoState.favFilePreview || seoState.faviconUrl" class="w-3 h-3 rounded-xs object-cover">
                                            </template>
                                            <span>GAOCHICKEN.VN</span>
                                        </div>
                                        <div class="text-xs font-black text-gray-900 line-clamp-1 leading-snug" x-text="seoState.metaTitle || 'GAO - Gà Sốt & Cơm Hà Nội'"></div>
                                        <div class="text-[11px] text-gray-600 line-clamp-2 leading-relaxed" x-text="seoState.metaDesc || 'Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội.'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. PREVIEW: KẾT QUẢ TÌM KIẾM GOOGLE -->
                            <div x-show="seoState.previewTab === 'google'" class="space-y-2">
                                <div class="text-[10px] text-gray-400 font-medium">
                                    Mô phỏng kết quả tìm kiếm trên Google Search:
                                </div>

                                <div class="bg-white p-3.5 rounded-xl border border-gray-300 shadow-sm space-y-1.5 text-left">
                                    <div class="flex items-center gap-2 text-xs">
                                        <div class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                            <template x-if="seoState.favFilePreview || (!seoState.favDeleted && seoState.faviconUrl)">
                                                <img :src="seoState.favFilePreview || seoState.faviconUrl" class="w-4 h-4 object-contain">
                                            </template>
                                            <template x-if="seoState.favDeleted || (!seoState.favFilePreview && !seoState.faviconUrl)">
                                                <span class="text-[10px]">🌐</span>
                                            </template>
                                        </div>
                                        <div class="space-y-0.5 leading-none">
                                            <div class="text-xs font-bold text-gray-900">GAO Gà Sốt & Cơm</div>
                                            <div class="text-[10px] font-mono text-emerald-800">https://gaochicken.vn</div>
                                        </div>
                                    </div>

                                    <div class="text-blue-700 font-bold text-xs hover:underline cursor-pointer line-clamp-1 pt-0.5" x-text="seoState.metaTitle || 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị'"></div>
                                    <div class="text-gray-600 text-[11px] line-clamp-2 leading-relaxed" x-text="seoState.metaDesc || 'Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội.'"></div>
                                </div>
                            </div>

                            <!-- 3. PREVIEW: TAB TRÌNH DUYỆT (CHROME / CỐC CỐC) -->
                            <div x-show="seoState.previewTab === 'browser'" class="space-y-2">
                                <div class="text-[10px] text-gray-400 font-medium">
                                    Thanh tiêu đề tab trên trình duyệt web:
                                </div>

                                <div class="bg-gray-800 p-2 rounded-xl border border-gray-700">
                                    <div class="bg-white px-3 py-2 rounded-lg border border-gray-300 shadow-xs flex items-center gap-2 max-w-full">
                                        <template x-if="seoState.favFilePreview || (!seoState.favDeleted && seoState.faviconUrl)">
                                            <img :src="seoState.favFilePreview || seoState.faviconUrl" class="w-4 h-4 rounded-xs object-contain shrink-0">
                                        </template>
                                        <template x-if="seoState.favDeleted || (!seoState.favFilePreview && !seoState.faviconUrl)">
                                            <span class="w-4 h-4 rounded-full bg-amber-500 text-[9px] flex items-center justify-center text-white shrink-0 font-black">G</span>
                                        </template>
                                        <span class="text-xs font-bold text-gray-800 truncate" x-text="seoState.metaTitle || 'GAO - Gà Sốt & Cơm'"></span>
                                        <span class="text-gray-400 text-xs ml-auto shrink-0">✕</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Gợi ý nhanh chuẩn SEO -->
                        <div class="bg-blue-50/70 p-3 rounded-2xl border border-blue-200/60 space-y-1 text-[11px] text-blue-950">
                            <span class="font-bold text-blue-900 flex items-center gap-1">
                                <span>💡</span>
                                <span>Mẹo SEO lên Top Google:</span>
                            </span>
                            <p class="text-gray-600 leading-relaxed text-[10px]">
                                Đặt từ khóa chính (vd: <em>Gà Sốt, Gà Rán, Cơm Gà Hà Nội</em>) ở đầu Tiêu Đề và Mô Tả để Google index đạt thứ hạng cao nhất.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- STICKY ACTION BOTTOM BAR (DỄ THAO TÁC KHI CUỘN) -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 flex-wrap gap-3">
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true"
                        class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>👁️ Xem Thử Website Trực Tiếp</span>
                    </button>

                    <div class="flex items-center gap-3">
                        <template x-if="seoState.isDirty">
                            <span class="text-xs font-bold text-amber-600 flex items-center gap-1 animate-pulse">
                                <span>⚠️</span>
                                <span>Chưa lưu thay đổi</span>
                            </span>
                        </template>

                        <button 
                            type="submit" 
                            class="px-6 py-2.5 rounded-xl text-white font-black text-xs uppercase tracking-wider shadow-md transition-all cursor-pointer flex items-center gap-1.5 hover:scale-105 active:scale-95"
                            :class="seoState.isDirty ? 'bg-gradient-to-r from-red-600 via-orange-600 to-amber-600 hover:from-red-700 hover:to-amber-700 shadow-red-500/30' : 'bg-red-600 hover:bg-red-700'"
                        >
                            <span>💾</span>
                            <span>Lưu Cấu Hình SEO & Hình Ảnh</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- FLOATING LIVE PREVIEW BUTTON (CỐ ĐỊNH GÓC DƯỚI) -->
    <div class="fixed bottom-6 right-6 z-40">
        <button 
            type="button" 
            @click="showWebPreviewModal = true"
            class="px-4 py-3 bg-gradient-to-r from-red-600 via-orange-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white font-black text-xs rounded-2xl shadow-xl shadow-red-500/30 flex items-center gap-2.5 transition-all duration-300 hover:scale-105 active:scale-95 cursor-pointer border border-white/20"
        >
            <span class="text-base animate-pulse">👁️</span>
            <span class="tracking-wide uppercase font-mono">Xem Trước Website</span>
        </button>
    </div>

    <!-- LIVE WEB PREVIEW MODAL (POPUP TOÀN BỘ TRANG WEB) -->
    <div 
        x-show="showWebPreviewModal" 
        x-cloak 
        class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <div 
            class="bg-gray-900 rounded-3xl w-full max-w-7xl h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-gray-700"
            @click.outside="showWebPreviewModal = false"
        >
            <!-- Top Toolbar Preview -->
            <div class="px-5 py-3.5 bg-gray-800 border-b border-gray-700 flex items-center justify-between gap-3 text-xs text-white">
                <div class="flex items-center gap-3">
                    <span class="font-black flex items-center gap-1.5 text-amber-400 text-sm">
                        <span>👁️</span>
                        <span>Xem Trước Giao Diện Website Trực Tiếp</span>
                    </span>
                    <span class="text-gray-400 hidden sm:inline text-[11px]">• Trực quan 100% trang chủ</span>
                </div>

                <!-- Device Mode Buttons -->
                <div class="flex items-center gap-2 bg-gray-900 p-1 rounded-xl border border-gray-700">
                    <button 
                        type="button" 
                        @click="previewDevice = 'desktop'"
                        :class="previewDevice === 'desktop' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-400 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg font-bold text-xs flex items-center gap-1.5 transition-all cursor-pointer"
                    >
                        <span>🖥️</span>
                        <span class="hidden sm:inline">Máy tính (Desktop)</span>
                    </button>
                    <button 
                        type="button" 
                        @click="previewDevice = 'mobile'"
                        :class="previewDevice === 'mobile' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-400 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg font-bold text-xs flex items-center gap-1.5 transition-all cursor-pointer"
                    >
                        <span>📱</span>
                        <span class="hidden sm:inline">Điện thoại (Mobile)</span>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="refreshPreviewIframe()"
                        class="p-2 rounded-xl bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-bold transition-colors cursor-pointer"
                        title="Tải lại trang xem trước"
                    >
                        🔄 Tải Lại
                    </button>
                    <a 
                        href="/" 
                        target="_blank" 
                        class="px-3 py-2 rounded-xl bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-bold transition-colors flex items-center gap-1"
                        title="Mở tab trình duyệt mới"
                    >
                        <span>Mở Tab Mới</span>
                        <span>↗</span>
                    </a>
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = false"
                        class="p-2 rounded-xl bg-rose-600/80 hover:bg-rose-600 text-white text-xs font-black transition-colors cursor-pointer"
                        title="Đóng cửa sổ xem trước"
                    >
                        ✕
                    </button>
                </div>
            </div>

            <!-- Iframe Container -->
            <div class="flex-1 bg-gray-950 flex items-center justify-center p-2 sm:p-4 overflow-auto">
                <div 
                    class="h-full transition-all duration-300 rounded-2xl overflow-hidden bg-white shadow-2xl border border-gray-700"
                    :class="previewDevice === 'desktop' ? 'w-full max-w-full' : 'w-[400px] max-w-full h-[780px]'"
                >
                    <iframe 
                        id="webPreviewIframe"
                        src="/" 
                        class="w-full h-full border-0"
                        title="Live Web Preview"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function settingsAdminData() {
    return {
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'store',
        showWebPreviewModal: false,
        previewDevice: 'desktop',
        
        setTab(tab) {
            this.activeTab = tab;
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        },

        refreshPreviewIframe() {
            const iframe = document.getElementById('webPreviewIframe');
            if (iframe) {
                iframe.src = iframe.src;
            }
        },

        // Realtime state cho Tab 1: Bếp & Giờ Mở Cửa
        storeState: {
            status: @json($settings['store_open_status'] ?? 'open'),
            openTime: @json($settings['kitchen_open_time'] ?? '09:30'),
            closeTime: @json($settings['kitchen_close_time'] ?? '22:30'),
            rushNote: @json($settings['rush_hour_note'] ?? 'Giờ trưa 11:30 - 13:00 quán có thể giao chậm hơn 10 phút, mong quý khách thông cảm!')
        },

        // Realtime state cho Tab 2: Phí ship & Simulator
        deliveryState: {
            baseDistance: {{ (float)($settings['shipping_base_distance'] ?? 3) }},
            baseFee: {{ (float)($settings['shipping_base_fee'] ?? 15000) }},
            perKmFee: {{ (float)($settings['shipping_per_km_fee'] ?? 5000) }},
            maxDistance: {{ (float)($settings['shipping_max_distance'] ?? 10) }},
            freeshipThreshold: {{ (float)($settings['freeship_threshold'] ?? 150000) }},
            estimateTime: @json($settings['delivery_time_estimate'] ?? '25 - 40 phút'),
            
            // Simulator values
            simDistance: 4,
            simSubtotal: 120000,
            
            get calculatedFee() {
                if (parseFloat(this.simSubtotal) >= parseFloat(this.freeshipThreshold)) {
                    return 0;
                }
                const dist = parseFloat(this.simDistance) || 0;
                const baseDist = parseFloat(this.baseDistance) || 0;
                const baseF = parseFloat(this.baseFee) || 0;
                const perKm = parseFloat(this.perKmFee) || 0;

                if (dist <= baseDist) {
                    return baseF;
                }
                const extraKm = Math.ceil(dist - baseDist);
                return baseF + (extraKm * perKm);
            },

            formatMoney(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
            }
        },

        // Realtime state cho VietQR Preview
        bankState: {
            bankCode: @json($settings['bank_code'] ?? 'MB'),
            bankName: @json($settings['bank_name'] ?? 'MB Bank (Quân Đội)'),
            accountNumber: @json($settings['bank_account_number'] ?? '0988888888'),
            accountHolder: @json($settings['bank_account_holder'] ?? 'GAO CHICKEN HA NOI'),
            prefix: @json($settings['bank_transfer_prefix'] ?? 'HUBBY')
        },

        // Realtime state cho SEO Preview & Live Image Validation
        seoState: {
            // Initial snapshot để so sánh dirty
            initial: {
                metaTitle: @json($settings['meta_title'] ?? 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị'),
                metaDesc: @json($settings['meta_description'] ?? 'Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội. Giao nhanh 25-40 phút.'),
                ogImageUrl: @json(!empty($settings['og_image']) ? (str_starts_with($settings['og_image'], 'http') ? $settings['og_image'] : asset($settings['og_image'])) : ''),
                ogRawValue: @json($settings['og_image'] ?? ''),
                faviconUrl: @json(!empty($settings['favicon_url']) ? (str_starts_with($settings['favicon_url'], 'http') ? $settings['favicon_url'] : asset($settings['favicon_url'])) : ''),
                favRawValue: @json($settings['favicon_url'] ?? '')
            },

            // Editable states
            metaTitle: @json($settings['meta_title'] ?? 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị'),
            metaDesc: @json($settings['meta_description'] ?? 'Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội. Giao nhanh 25-40 phút.'),
            
            // OG Image
            ogImageUrl: @json(!empty($settings['og_image']) ? (str_starts_with($settings['og_image'], 'http') ? $settings['og_image'] : asset($settings['og_image'])) : ''),
            ogRawValue: @json($settings['og_image'] ?? ''),
            ogFilePreview: null,
            ogFileName: '',
            ogFileSizeStr: '',
            ogDimensions: null,
            ogValidation: { status: 'none', message: '', type: 'info' },
            ogDeleted: false,

            // Favicon
            faviconUrl: @json(!empty($settings['favicon_url']) ? (str_starts_with($settings['favicon_url'], 'http') ? $settings['favicon_url'] : asset($settings['favicon_url'])) : ''),
            favRawValue: @json($settings['favicon_url'] ?? ''),
            favFilePreview: null,
            favFileName: '',
            favFileSizeStr: '',
            favDimensions: null,
            favValidation: { status: 'none', message: '', type: 'info' },
            favDeleted: false,

            // Preview active tab
            previewTab: 'social', // 'social' | 'google' | 'browser'

            // Check if form is dirty
            get isDirty() {
                return (this.metaTitle !== this.initial.metaTitle) ||
                       (this.metaDesc !== this.initial.metaDesc) ||
                       (this.ogFilePreview !== null) ||
                       (this.ogDeleted) ||
                       (this.favFilePreview !== null) ||
                       (this.favDeleted);
            },

            // Title validation info
            get titleLength() {
                return (this.metaTitle || '').length;
            },
            get titleStatus() {
                const len = this.titleLength;
                if (len === 0) return { label: 'Chưa nhập tiêu đề', color: 'text-gray-400', barColor: 'bg-gray-200', pct: 0 };
                if (len < 35) return { label: 'Hơi ngắn (khuyến nghị 40 - 60 ký tự)', color: 'text-amber-500', barColor: 'bg-amber-400', pct: Math.min(100, Math.round((len / 60) * 100)) };
                if (len <= 60) return { label: 'Tối ưu chuẩn SEO (40 - 60 ký tự)', color: 'text-emerald-600 font-bold', barColor: 'bg-emerald-500', pct: 100 };
                if (len <= 70) return { label: 'Hơi dài (gần chạm giới hạn hiển thị)', color: 'text-amber-600', barColor: 'bg-amber-500', pct: 100 };
                return { label: 'Quá dài (sẽ bị Google cắt bớt ...)', color: 'text-rose-600 font-bold', barColor: 'bg-rose-500', pct: 100 };
            },

            // Description validation info
            get descLength() {
                return (this.metaDesc || '').length;
            },
            get descStatus() {
                const len = this.descLength;
                if (len === 0) return { label: 'Chưa nhập mô tả', color: 'text-gray-400', barColor: 'bg-gray-200', pct: 0 };
                if (len < 100) return { label: 'Hơi ngắn (khuyến nghị 120 - 160 ký tự)', color: 'text-amber-500', barColor: 'bg-amber-400', pct: Math.min(100, Math.round((len / 160) * 100)) };
                if (len <= 160) return { label: 'Tối ưu chuẩn SEO (120 - 160 ký tự)', color: 'text-emerald-600 font-bold', barColor: 'bg-emerald-500', pct: 100 };
                if (len <= 180) return { label: 'Hơi dài (sắp chạm giới hạn)', color: 'text-amber-600', barColor: 'bg-amber-500', pct: 100 };
                return { label: 'Quá dài (sẽ bị rút gọn trên kết quả tìm kiếm)', color: 'text-rose-600 font-bold', barColor: 'bg-rose-500', pct: 100 };
            },

            // OG Image Upload & Validation
            handleOgFile(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.ogDeleted = false;

                const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    this.ogValidation = {
                        status: 'error',
                        type: 'error',
                        message: '❌ Định dạng không hỗ trợ. Vui lòng chọn file ảnh JPG, PNG hoặc WEBP.'
                    };
                    this.ogFilePreview = null;
                    this.ogFileName = file.name;
                    return;
                }

                const sizeKB = Math.round(file.size / 1024);
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                this.ogFileSizeStr = file.size > 1024 * 1024 ? `${sizeMB} MB` : `${sizeKB} KB`;

                if (file.size > 5 * 1024 * 1024) {
                    this.ogValidation = {
                        status: 'error',
                        type: 'error',
                        message: `❌ Dung lượng file quá lớn (${this.ogFileSizeStr}). Tối đa cho phép 5MB.`
                    };
                    this.ogFilePreview = null;
                    this.ogFileName = file.name;
                    return;
                }

                this.ogFileName = file.name;
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.ogFilePreview = ev.target.result;

                    const img = new Image();
                    img.onload = () => {
                        const w = img.naturalWidth;
                        const h = img.naturalHeight;
                        this.ogDimensions = { width: w, height: h };

                        if (w >= 1200 && h >= 630) {
                            this.ogValidation = {
                                status: 'valid',
                                type: 'success',
                                message: `✅ Ảnh xuất sắc (${w}x${h}px • ${this.ogFileSizeStr}): Đạt chuẩn HD tỉ lệ 1.91:1, hiển thị cực đẹp trên Facebook & Zalo.`
                            };
                        } else if (w >= 600 && h >= 315) {
                            this.ogValidation = {
                                status: 'valid',
                                type: 'warning',
                                message: `🟡 Kích thước tốt (${w}x${h}px • ${this.ogFileSizeStr}): Đủ điều kiện hiển thị rõ ràng (khuyến nghị 1200x630px).`
                            };
                        } else {
                            this.ogValidation = {
                                status: 'warning',
                                type: 'warning',
                                message: `⚠️ Kích thước nhỏ (${w}x${h}px • ${this.ogFileSizeStr}): Có thể bị mờ khi chia sẻ trên mạng xã hội. Nên dùng ảnh từ 600x315px trở lên.`
                            };
                        }
                    };
                    img.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            },

            cancelOgUpload() {
                this.ogFilePreview = null;
                this.ogFileName = '';
                this.ogFileSizeStr = '';
                this.ogDimensions = null;
                this.ogValidation = { status: 'none', message: '', type: 'info' };
                const fileInput = document.getElementById('og_image_file_input');
                if (fileInput) fileInput.value = '';
            },

            deleteOgImage() {
                this.cancelOgUpload();
                this.ogDeleted = true;
                this.ogRawValue = '';
                this.ogImageUrl = '';
            },

            restoreOgImage() {
                this.cancelOgUpload();
                this.ogDeleted = false;
                this.ogRawValue = this.initial.ogRawValue;
                this.ogImageUrl = this.initial.ogImageUrl;
            },

            // Favicon Upload & Validation
            handleFaviconFile(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.favDeleted = false;

                const sizeKB = Math.round(file.size / 1024);
                this.favFileSizeStr = `${sizeKB} KB`;

                if (file.size > 2 * 1024 * 1024) {
                    this.favValidation = {
                        status: 'error',
                        type: 'error',
                        message: `❌ Dung lượng icon quá lớn (${this.favFileSizeStr}). Tối đa cho phép 2MB.`
                    };
                    this.favFilePreview = null;
                    this.favFileName = file.name;
                    return;
                }

                this.favFileName = file.name;
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.favFilePreview = ev.target.result;

                    const img = new Image();
                    img.onload = () => {
                        const w = img.naturalWidth;
                        const h = img.naturalHeight;
                        this.favDimensions = { width: w, height: h };

                        this.favValidation = {
                            status: 'valid',
                            type: 'success',
                            message: `✅ Icon hợp lệ (${w}x${h}px • ${this.favFileSizeStr}): Tự động tối ưu trên tab trình duyệt.`
                        };
                    };
                    img.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            },

            cancelFavUpload() {
                this.favFilePreview = null;
                this.favFileName = '';
                this.favFileSizeStr = '';
                this.favDimensions = null;
                this.favValidation = { status: 'none', message: '', type: 'info' };
                const fileInput = document.getElementById('favicon_file_input');
                if (fileInput) fileInput.value = '';
            },

            deleteFavicon() {
                this.cancelFavUpload();
                this.favDeleted = true;
                this.favRawValue = '';
                this.faviconUrl = '';
            },

            restoreFavicon() {
                this.cancelFavUpload();
                this.favDeleted = false;
                this.favRawValue = this.initial.favRawValue;
                this.faviconUrl = this.initial.faviconUrl;
            }
        }
    };
}
</script>
@endsection
