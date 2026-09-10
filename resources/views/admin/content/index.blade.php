@extends('layouts.admin')

@section('title', 'Quản Lý Giao Diện & Nội Dung Trang Chủ')
@section('page_title', '🎨 Quản Lý Giao Diện & Nội Dung Trang Chủ')

@section('content')
<div 
    class="max-w-6xl space-y-6 pb-28 relative" 
    x-data="contentAdminData()"
    @click="activeMenuReviewId = null"
>

    <!-- FLOATING TOAST NOTIFICATION -->
    <div 
        x-show="toastMessage" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-24 md:bottom-8 right-6 z-50 bg-gray-900 text-white px-4 py-2.5 rounded-2xl shadow-2xl border border-gray-700 flex items-center gap-2.5 text-xs font-bold"
        x-cloak
    >
        <span class="text-sm">🎨</span>
        <span x-text="toastMessage"></span>
    </div>

    <!-- FLOATING PREVIEW BUTTON (CỐ ĐỊNH Ở GÓC PHẢI MÀN HÌNH - XEM TRƯỚC MỌI LÚC) -->
    <div class="fixed bottom-6 right-6 z-40 flex items-center gap-2 shadow-2xl rounded-2xl p-1.5 bg-gray-900/90 backdrop-blur-md border border-gray-700">
        <button 
            type="button" 
            @click="showWebPreviewModal = true; refreshPreviewIframe()"
            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white font-black text-xs uppercase tracking-wider shadow-lg flex items-center gap-2 cursor-pointer transition-all hover:scale-105 active:scale-95"
            title="Mở cửa sổ xem trước website trực tiếp"
        >
            <span class="text-sm">👁️</span>
            <span>Xem Trước Website</span>
        </button>

        <a 
            href="/" 
            target="_blank" 
            class="p-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-bold transition-all hover:text-white flex items-center justify-center cursor-pointer"
            title="Mở website trong Tab mới"
        >
            <span>↗</span>
        </a>
    </div>

    <!-- 1. HEADER (TITLE + CÁC NÚT XEM TRƯỚC TIỆN LỢI) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>🎨 Quản Lý Giao Diện & Nội Dung Trang Chủ</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">
                Tự do thay đổi khẩu hiệu, tiêu đề, bật/tắt các khối nội dung và xem trước trực quan.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button 
                type="button" 
                @click="showWebPreviewModal = true; refreshPreviewIframe()"
                class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs transition-all shadow-sm flex items-center gap-1.5 cursor-pointer"
            >
                <span>👁️ Xem Trước Trang Chủ</span>
            </button>

            <a 
                href="/" 
                target="_blank" 
                class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition-colors flex items-center gap-1.5"
            >
                <span>🌐 Mở Tab Mới</span>
                <span>↗</span>
            </a>
        </div>
    </div>

    <!-- 2. TAB NAVIGATION: THỨ TỰ TỪ TRÊN XUỐNG DƯỚI CỦA TRANG WEB -->
    <div class="bg-white p-1.5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-1 overflow-x-auto text-xs font-bold scrollbar-thin">
        
        <button 
            type="button" 
            @click="setTab('hero')" 
            :class="activeTab === 'hero' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🌟 1. Banner Hero Chính</span>
        </button>

        <button 
            type="button" 
            @click="setTab('header')" 
            :class="activeTab === 'header' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🔝 2. Đầu Trang (Header)</span>
        </button>

        <button 
            type="button" 
            @click="setTab('sections')" 
            :class="activeTab === 'sections' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>📑 3. Khối Nội Dung (Sections)</span>
        </button>

        <button 
            type="button" 
            @click="setTab('benefits')" 
            :class="activeTab === 'benefits' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🛡️ 4. 3 Cam Kết Vàng</span>
        </button>

        <button 
            type="button" 
            @click="setTab('testimonials')" 
            :class="activeTab === 'testimonials' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>💬 5. Đánh Giá Khách Hàng</span>
        </button>

        <button 
            type="button" 
            @click="setTab('popup')" 
            :class="activeTab === 'popup' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🎁 6. Popup Khuyến Mãi</span>
        </button>

        <button 
            type="button" 
            @click="setTab('footer')" 
            :class="activeTab === 'footer' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🔻 7. Chân Trang & Liên Hệ</span>
        </button>

    </div>

    <!-- TAB 1: 🌟 BANNER HERO CHÍNH -->
    <div x-show="activeTab === 'hero'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.content.hero.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.content.index', ['tab' => 'hero']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-6">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🌟</span>
                            <span>Tùy Chỉnh Toàn Diện Banner Hero Trang Chủ</span>
                        </h3>
                        <p class="text-xs text-gray-400">Khẩu hiệu, tiêu đề nổi bật, 2 nút bấm đặt món, 3 điểm cam kết nhanh và hình ảnh món gà</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="showWebPreviewModal = true; refreshPreviewIframe()"
                            class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-black text-xs uppercase tracking-wider transition-all cursor-pointer flex items-center gap-1.5"
                        >
                            <span>👁️</span>
                            <span>Xem Thử Website</span>
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md transition-all cursor-pointer flex items-center gap-1.5"
                        >
                            <span>💾</span>
                            <span>Lưu Banner Hero</span>
                        </button>
                    </div>
                </div>

                <!-- LIVE PREVIEW CHUẨN 100% GIAO DIỆN THẬT -->
                <div class="p-5 sm:p-6 rounded-3xl bg-[#FFFDF8] border-2 border-orange-200/70 shadow-inner space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black uppercase text-amber-900 tracking-wider flex items-center gap-1.5">
                            <span>👁️</span>
                            <span>Mô Phỏng Trực Quan (Live Preview như ngoài Website):</span>
                        </span>
                        <span class="text-[10px] text-gray-400 italic">Nội dung sẽ cập nhật ngay khi bạn gõ chữ bên dưới</span>
                    </div>

                    <div class="bg-gradient-to-b from-[#FFFDF8] to-[#FFF6EB] p-6 sm:p-8 rounded-2xl border border-orange-100 shadow-sm grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        
                        <!-- Left Preview Column -->
                        <div class="lg:col-span-7 space-y-4">
                            <template x-if="hero.badge">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 border border-red-200 text-red-600 font-extrabold text-[11px] tracking-wider uppercase shadow-2xs">
                                    <span>✦</span>
                                    <span x-text="hero.badge"></span>
                                </div>
                            </template>

                            <div class="space-y-0.5">
                                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-gray-900 leading-tight">
                                    <span x-text="hero.title || 'GÀ GIÒN.'"></span><br>
                                    <span class="text-red-600 inline-block" x-text="hero.title_highlight || 'SỐT ĐẬM.'"></span>
                                </h1>
                            </div>

                            <p class="text-gray-600 text-xs sm:text-sm font-medium leading-relaxed" x-text="hero.subtitle"></p>

                            <!-- 2 CTA Buttons Preview -->
                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <div class="px-5 py-2.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 text-white font-black text-xs uppercase shadow-md flex items-center gap-1.5">
                                    <span x-text="hero.cta_primary_text || '🍗 ĐẶT MÓN NGAY'"></span>
                                </div>

                                <template x-if="hero.cta_secondary_text">
                                    <div class="px-4 py-2.5 rounded-full bg-white text-gray-800 font-bold text-xs border border-gray-200 shadow-2xs flex items-center gap-1.5">
                                        <span x-text="hero.cta_secondary_text"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- 3 Quick Badges Preview -->
                            <div class="pt-2 flex flex-wrap items-center gap-4 text-xs font-bold text-gray-600 border-t border-orange-100/60">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-red-500">⚡</span>
                                    <span x-text="hero.delivery_time || 'Giao 25–40p'"></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-amber-500">🔥</span>
                                    <span x-text="hero.hot_status || 'Luôn nóng giòn'"></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-yellow-500">⭐</span>
                                    <span x-text="hero.rating || 'Đánh giá 4.9/5'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Preview Column: Hero Image -->
                        <div class="lg:col-span-5 relative flex justify-center">
                            <div class="relative w-full max-w-xs rounded-2xl overflow-hidden shadow-xl border-4 border-white aspect-[4/3] bg-gray-900">
                                <img 
                                    :src="hero.filePreview || hero.imagePreview" 
                                    class="w-full h-full object-cover"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                                <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur-xs py-1 px-3 rounded-full shadow-md border border-red-100 flex items-center gap-1.5">
                                    <span class="text-[9px] font-bold uppercase text-gray-500">CHỈ TỪ</span>
                                    <span class="text-sm font-black text-red-600" x-text="Number(hero.price || 49000).toLocaleString('vi-VN') + 'đ'"></span>
                                </div>

                                <template x-if="hero.floating_badge">
                                    <div class="absolute top-2.5 left-2.5 bg-red-600 text-white font-black text-[10px] px-2.5 py-1 rounded-full shadow-md flex items-center gap-1" x-text="hero.floating_badge"></div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FORM CẤU HÌNH CHI TIẾT THEO TỪNG NHÓM -->
                <div class="space-y-6 pt-2">
                    
                    <!-- Nhóm 1: Tiêu đề & Giới thiệu -->
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-3">
                        <h4 class="font-extrabold text-xs text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>📝</span>
                            <span>1. Khẩu Hiệu, Tiêu Đề & Mô Tả</span>
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="space-y-1 sm:col-span-3">
                                <label class="block font-bold text-gray-700">Huy hiệu nhỏ trên cùng (Tagline / Badge)</label>
                                <input 
                                    type="text" 
                                    name="badge"
                                    x-model="hero.badge"
                                    placeholder="VD: ✦ GÀ CHIÊN + SỐT ĐẬM VỊ"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-bold text-red-600 focus:border-red-500 outline-none"
                                >
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">Tiêu đề chính (Dòng 1 - Chữ đen)</label>
                                <input 
                                    type="text" 
                                    name="title"
                                    x-model="hero.title"
                                    placeholder="VD: GÀ GIÒN."
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-black text-gray-900 focus:border-red-500 outline-none"
                                    required
                                >
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <label class="block font-bold text-gray-700">Tiêu đề nổi bật (Dòng 2 - Chữ đỏ to)</label>
                                <input 
                                    type="text" 
                                    name="title_highlight"
                                    x-model="hero.title_highlight"
                                    placeholder="VD: SỐT ĐẬM."
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-black text-red-600 focus:border-red-500 outline-none"
                                >
                            </div>

                            <div class="space-y-1 sm:col-span-3">
                                <label class="block font-bold text-gray-700">Đoạn văn mô tả giới thiệu món ăn</label>
                                <textarea 
                                    name="subtitle"
                                    x-model="hero.subtitle"
                                    rows="2"
                                    placeholder="VD: Gà nóng giòn, phủ sốt nguyên bản. Giao tận nơi tại Hà Nội trong 25–40 phút."
                                    class="w-full px-3.5 py-2 rounded-xl bg-white border border-gray-200 font-medium text-gray-800 focus:border-red-500 outline-none"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Nhóm 2: Nút bấm Kêu gọi hành động (CTA) -->
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-3">
                        <h4 class="font-extrabold text-xs text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🔘</span>
                            <span>2. Cài Đặt 2 Nút Bấm Kêu Gọi Hành Động (CTA)</span>
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <!-- Nút chính (Đỏ) -->
                            <div class="p-3 bg-white rounded-xl border border-red-100 space-y-2">
                                <span class="font-bold text-red-600 block">🔴 Nút Bấm Chính (Màu Đỏ Nổi Bật)</span>
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-semibold text-gray-600">Chữ trên nút</label>
                                    <input 
                                        type="text" 
                                        name="cta_primary_text"
                                        x-model="hero.cta_primary_text"
                                        placeholder="VD: 🍗 ĐẶT MÓN NGAY"
                                        class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                                    >
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-semibold text-gray-600">Đường dẫn khi click (URL)</label>
                                    <input 
                                        type="text" 
                                        name="cta_primary_url"
                                        x-model="hero.cta_primary_url"
                                        placeholder="VD: /menu"
                                        class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 font-mono text-gray-800 outline-none focus:border-red-500"
                                    >
                                </div>
                            </div>

                            <!-- Nút phụ (Trắng) -->
                            <div class="p-3 bg-white rounded-xl border border-gray-200 space-y-2">
                                <span class="font-bold text-gray-800 block">⚪ Nút Bấm Phụ (Màu Trắng Viền Xám)</span>
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-semibold text-gray-600">Chữ trên nút</label>
                                    <input 
                                        type="text" 
                                        name="cta_secondary_text"
                                        x-model="hero.cta_secondary_text"
                                        placeholder="VD: 🔥 XEM 4 VỊ SỐT"
                                        class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                                    >
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-semibold text-gray-600">Đường dẫn khi click (URL)</label>
                                    <input 
                                        type="text" 
                                        name="cta_secondary_url"
                                        x-model="hero.cta_secondary_url"
                                        placeholder="VD: /menu hoặc /cam-ket"
                                        class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 font-mono text-gray-800 outline-none focus:border-red-500"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nhóm 3: 3 Điểm Nổi Bật Dưới Nút Bấm -->
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-3">
                        <h4 class="font-extrabold text-xs text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>⚡</span>
                            <span>3. 3 Điểm Nổi Bật (Cam Kết Nhanh Dưới Nút Bấm)</span>
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">⚡ Thời gian giao hàng</label>
                                <input 
                                    type="text" 
                                    name="delivery_time"
                                    x-model="hero.delivery_time"
                                    placeholder="VD: Giao 25–40p"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">🔥 Trạng thái nhiệt độ món</label>
                                <input 
                                    type="text" 
                                    name="hot_status"
                                    x-model="hero.hot_status"
                                    placeholder="VD: Luôn nóng giòn"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">⭐ Đánh giá / Sao uy tín</label>
                                <input 
                                    type="text" 
                                    name="rating"
                                    x-model="hero.rating"
                                    placeholder="VD: Đánh giá 4.9/5"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Nhóm 4: Hình Ảnh Banner Hero & Giá Bán -->
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-3">
                        <h4 class="font-extrabold text-xs text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🖼️</span>
                            <span>4. Hình Ảnh Món Ăn Hero & Giá Bán Hiển Thị</span>
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">Giá bán khởi điểm "Chỉ từ" (VNĐ)</label>
                                <input 
                                    type="number" 
                                    name="price"
                                    x-model="hero.price"
                                    step="1000"
                                    min="0"
                                    placeholder="VD: 49000"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-black text-red-600 outline-none focus:border-red-500 font-mono"
                                >
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <label class="block font-bold text-gray-700">Huy hiệu nổi góc ảnh (Floating Badge)</label>
                                <input 
                                    type="text" 
                                    name="floating_badge"
                                    x-model="hero.floating_badge"
                                    placeholder="VD: 🔥 MÓN MỚI RA MẮT • ĐẶT NGAY"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <label class="block font-bold text-gray-700">Link URL hình ảnh Hero (Tùy chọn)</label>
                                <input 
                                    type="text" 
                                    name="image"
                                    x-model="hero.image"
                                    placeholder="https://images.unsplash.com/..."
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 font-mono text-gray-800 outline-none focus:border-red-500"
                                >
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">Hoặc Tải ảnh mới lên từ máy tính</label>
                                <input 
                                    type="file" 
                                    name="hero_image_file"
                                    @change="handleHeroFile"
                                    accept="image/*"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-medium cursor-pointer"
                                >
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 gap-2">
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true; refreshPreviewIframe()"
                        class="px-5 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-black text-xs uppercase tracking-wider transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span>👁️</span>
                        <span>Xem Thử Website</span>
                    </button>
                    <button 
                        type="submit" 
                        class="px-8 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md transition-all cursor-pointer flex items-center gap-2"
                    >
                        <span>💾</span>
                        <span>Lưu Toàn Bộ Cấu Hình Banner Hero</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 2: 🔝 HEADER (ĐẦU TRANG & THANH THÔNG BÁO) -->
    <div x-show="activeTab === 'header'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.content.index', ['tab' => 'header']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🔝</span>
                            <span>Cấu Hình Header & Thanh Thông Báo Đầu Trang</span>
                        </h3>
                        <p class="text-xs text-gray-400">Thông báo khuyến mãi chạy trên cùng, số hotline và nút kêu gọi đặt món</p>
                    </div>
                </div>

                <!-- LIVE PREVIEW CHUẨN 100% GIAO DIỆN THẬT ĐẦU TRANG (TOP BAR & HEADER) -->
                <div class="p-4 sm:p-5 rounded-3xl bg-[#FFFDF8] border-2 border-orange-200/70 shadow-inner space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black uppercase text-amber-900 tracking-wider flex items-center gap-1.5">
                            <span>👁️</span>
                            <span>Mô Phỏng Trực Quan Đầu Trang (Live Preview như ngoài Website):</span>
                        </span>
                        <span class="text-[10px] text-gray-400 italic">Tự động cập nhật khi bạn gõ chữ bên dưới</span>
                    </div>

                    <div class="rounded-2xl overflow-hidden border border-orange-200/90 shadow-sm space-y-0">
                        <!-- Top Bar Preview -->
                        <div class="bg-gradient-to-r from-red-600 to-amber-600 text-white text-xs font-semibold py-2 px-4 text-center tracking-wide flex items-center justify-center gap-2 flex-wrap shadow-xs">
                            <span class="inline-block animate-pulse">🔥</span>
                            <span x-text="headerState.topNotification || 'Ưu đãi hôm nay: Freeship bán kính 3km cho đơn hàng từ 100k!'"></span>
                            <span class="hidden sm:inline" x-text="'• Hotline đặt món: ' + (headerState.hotline || '0988.868.GAO')"></span>
                        </div>

                        <!-- Main Navbar Preview -->
                        <div class="bg-white p-3.5 sm:px-5 flex items-center justify-between gap-4 border-b border-orange-100 flex-wrap">
                            <!-- Brand Logo & Slogan -->
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-red-600 via-orange-600 to-amber-500 flex items-center justify-center text-white shadow-xs">
                                    <span class="text-sm sm:text-base font-black">🍗</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-base sm:text-lg font-black tracking-tight text-gray-900 leading-none" x-text="headerState.siteName || 'GAO'"></span>
                                        <span class="text-[9px] uppercase font-extrabold px-1.5 py-0.5 bg-red-100 text-red-700 rounded-sm" x-text="headerState.locationBadge || 'HÀ NỘI'"></span>
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-400 tracking-wider block mt-0.5" x-text="headerState.siteTagline || 'GÀ SỐT & CƠM'"></span>
                                </div>
                            </div>

                            <!-- Menu Links Mockup -->
                            <div class="hidden md:flex items-center gap-4 text-xs font-bold text-gray-600">
                                <span class="text-red-600 border-b-2 border-red-600 pb-0.5">Trang chủ</span>
                                <span class="hover:text-red-600">Thực đơn</span>
                                <span class="hover:text-red-600">4 Vị Sốt</span>
                                <span class="hover:text-red-600">Ưu đãi</span>
                            </div>

                            <!-- CTA Button Preview -->
                            <div>
                                <div class="px-4 py-2 rounded-full bg-gradient-to-r from-red-600 to-amber-600 text-white font-black text-xs shadow-xs flex items-center gap-1.5">
                                    <span x-text="headerState.ctaText || '🍗 Đặt món ngay'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1 md:col-span-2">
                        <label class="block font-bold text-gray-700">Dòng chữ thông báo khuyến mãi đầu trang (Top Notification)</label>
                        <input 
                            type="text" 
                            name="top_notification" 
                            x-model="headerState.topNotification"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Số điện thoại Hotline</label>
                        <input 
                            type="text" 
                            name="hotline" 
                            x-model="headerState.hotline"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Tên thương hiệu Header (Logo Text)</label>
                        <input 
                            type="text" 
                            name="site_name" 
                            x-model="headerState.siteName"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Khẩu hiệu phụ dưới Logo</label>
                        <input 
                            type="text" 
                            name="site_tagline" 
                            x-model="headerState.siteTagline"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Khu vực phục vụ (Badge hiển thị)</label>
                        <input 
                            type="text" 
                            name="location_badge" 
                            x-model="headerState.locationBadge"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Chữ nút bấm Header</label>
                        <input 
                            type="text" 
                            name="header_cta_text" 
                            x-model="headerState.ctaText"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Link nút bấm Header</label>
                        <input 
                            type="text" 
                            name="header_cta_url" 
                            x-model="headerState.ctaUrl"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cài Đặt Header
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 3: 📑 KHỐI NỘI DUNG (SECTIONS TRANG CHỦ) -->
    <div x-show="activeTab === 'sections'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.content.index', ['tab' => 'sections']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-6">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>📑</span>
                            <span>Cấu Hình & Bật / Tắt Các Khối Trên Trang Chủ</span>
                        </h3>
                        <p class="text-xs text-gray-400">Tùy ý ẩn hoặc hiện các khối (4 Vị Sốt, Món Bán Chạy, Combo, Cam Kết, Đánh Giá)</p>
                    </div>

                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true; refreshPreviewIframe()"
                        class="px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-black text-xs transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span>👁️</span>
                        <span>Xem Thử Trang Web Thực Tế</span>
                    </button>
                </div>



                <!-- FORM NHẬP LIỆU & BẬT/TẮT TỪNG KHỐI -->
                <div class="space-y-5">
                    
                    <!-- 1. Khối 4 Vị Sốt -->
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="font-bold text-xs text-gray-900 flex items-center gap-2">
                                <span>🌶️</span>
                                <span>1. Khối "4 Vị Sốt Đặc Trưng"</span>
                            </div>
                            <div class="w-52">
                                <select 
                                    name="section_sauces_enabled" 
                                    x-model="sectionsState.saucesEnabled"
                                    class="w-full px-3 py-1.5 rounded-xl bg-white border border-gray-300 font-black text-xs text-gray-800 outline-none focus:border-red-500 cursor-pointer"
                                >
                                    <option value="1">🟢 BẬT (Hiện trên web)</option>
                                    <option value="0">⚫ TẮT (Ẩn khỏi web)</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                            <div class="space-y-1">
                                <label class="block font-semibold text-gray-700">Huy hiệu nhỏ (Badge)</label>
                                <input 
                                    type="text" 
                                    name="home_sauces_badge" 
                                    x-model="sectionsState.saucesBadge"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label class="block font-semibold text-gray-700">Tiêu đề khối</label>
                                <input 
                                    type="text" 
                                    name="home_sauces_title" 
                                    x-model="sectionsState.saucesTitle"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-black text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>
                            <div class="space-y-1 md:col-span-3">
                                <label class="block font-semibold text-gray-700">Đoạn mô tả ngắn</label>
                                <input 
                                    type="text" 
                                    name="home_sauces_subtitle" 
                                    x-model="sectionsState.saucesSubtitle"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-medium text-gray-800 outline-none focus:border-red-500"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- 2. Khối Món Được Gọi Nhiều (Top Best Seller) -->
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="font-bold text-xs text-gray-900 flex items-center gap-2">
                                <span>🔥</span>
                                <span>2. Khối "Món Được Gọi Nhiều" (Top Best Seller)</span>
                            </div>
                            <div class="w-52">
                                <select 
                                    name="section_popular_enabled" 
                                    x-model="sectionsState.popularEnabled"
                                    class="w-full px-3 py-1.5 rounded-xl bg-white border border-gray-300 font-black text-xs text-gray-800 outline-none focus:border-red-500 cursor-pointer"
                                >
                                    <option value="1">🟢 BẬT (Hiện trên web)</option>
                                    <option value="0">⚫ TẮT (Ẩn khỏi web)</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                            <div class="space-y-1">
                                <label class="block font-semibold text-gray-700">Huy hiệu nhỏ (Badge)</label>
                                <input 
                                    type="text" 
                                    name="home_popular_badge" 
                                    x-model="sectionsState.popularBadge"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label class="block font-semibold text-gray-700">Tiêu đề khối</label>
                                <input 
                                    type="text" 
                                    name="home_popular_title" 
                                    x-model="sectionsState.popularTitle"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-black text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>
                            <div class="space-y-1 md:col-span-3">
                                <label class="block font-semibold text-gray-700">Đoạn mô tả ngắn</label>
                                <input 
                                    type="text" 
                                    name="home_popular_subtitle" 
                                    x-model="sectionsState.popularSubtitle"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-medium text-gray-800 outline-none focus:border-red-500"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- 3. Khối Ăn Combo, Lời Hơn -->
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="font-bold text-xs text-gray-900 flex items-center gap-2">
                                <span>🍱</span>
                                <span>3. Khối "Ăn Combo, Lời Hơn"</span>
                            </div>
                            <div class="w-52">
                                <select 
                                    name="section_combos_enabled" 
                                    x-model="sectionsState.combosEnabled"
                                    class="w-full px-3 py-1.5 rounded-xl bg-white border border-gray-300 font-black text-xs text-gray-800 outline-none focus:border-red-500 cursor-pointer"
                                >
                                    <option value="1">🟢 BẬT (Hiện trên web)</option>
                                    <option value="0">⚫ TẮT (Ẩn khỏi web)</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            <div class="space-y-1">
                                <label class="block font-semibold text-gray-700">Tiêu đề khối</label>
                                <input 
                                    type="text" 
                                    name="home_combos_title" 
                                    x-model="sectionsState.combosTitle"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-black text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>
                            <div class="space-y-1">
                                <label class="block font-semibold text-gray-700">Đoạn mô tả ngắn</label>
                                <input 
                                    type="text" 
                                    name="home_combos_subtitle" 
                                    x-model="sectionsState.combosSubtitle"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-medium text-gray-800 outline-none focus:border-red-500"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- 4. Khối 3 Cam Kết Vàng -->
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="font-bold text-xs text-gray-900 flex items-center gap-2">
                                <span>🛡️</span>
                                <span>4. Khối "3 Cam Kết Chất Lượng Vàng"</span>
                            </div>
                            <div class="w-52">
                                <select 
                                    name="section_benefits_enabled" 
                                    x-model="sectionsState.benefitsEnabled"
                                    class="w-full px-3 py-1.5 rounded-xl bg-white border border-gray-300 font-black text-xs text-gray-800 outline-none focus:border-red-500 cursor-pointer"
                                >
                                    <option value="1">🟢 BẬT (Hiện trên web)</option>
                                    <option value="0">⚫ TẮT (Ẩn khỏi web)</option>
                                </select>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Chi tiết nội dung 3 cam kết bạn có thể chỉnh sửa tại tab <strong>🛡️ 4. 3 Cam Kết Vàng</strong>.</p>
                    </div>

                    <!-- 5. Khối Đánh Giá Khách Hàng -->
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="font-bold text-xs text-gray-900 flex items-center gap-2">
                                <span>💬</span>
                                <span>5. Khối "Khách Ăn Nói Gì" (Đánh Giá Khách Hàng)</span>
                            </div>
                            <div class="w-52">
                                <select 
                                    name="section_testimonials_enabled" 
                                    x-model="sectionsState.testimonialsEnabled"
                                    class="w-full px-3 py-1.5 rounded-xl bg-white border border-gray-300 font-black text-xs text-gray-800 outline-none focus:border-red-500 cursor-pointer"
                                >
                                    <option value="1">🟢 BẬT (Hiện trên web)</option>
                                    <option value="0">⚫ TẮT (Ẩn khỏi web)</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            <div class="space-y-1">
                                <label class="block font-semibold text-gray-700">Tiêu đề khối</label>
                                <input 
                                    type="text" 
                                    name="home_testimonials_title" 
                                    x-model="sectionsState.testimonialsTitle"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-black text-gray-900 outline-none focus:border-red-500"
                                >
                            </div>
                            <div class="space-y-1">
                                <label class="block font-semibold text-gray-700">Đoạn mô tả ngắn</label>
                                <input 
                                    type="text" 
                                    name="home_testimonials_subtitle" 
                                    x-model="sectionsState.testimonialsSubtitle"
                                    class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 font-medium text-gray-800 outline-none focus:border-red-500"
                                >
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 gap-2">
                    <button 
                        type="button" 
                        @click="showWebPreviewModal = true; refreshPreviewIframe()"
                        class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-black text-xs uppercase tracking-wider transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span>👁️</span>
                        <span>Xem Thử Website</span>
                    </button>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cấu Hình Khối Nội Dung
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 4: 🛡️ 3 CAM KẾT CHẤT LƯỢNG -->
    <div x-show="activeTab === 'benefits'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🛡️</span>
                    <span>3 Cam Kết Chất Lượng Vàng Của Thương Hiệu</span>
                </h3>
                <p class="text-xs text-gray-400">Hiển thị ngay trên trang chủ và trang Cam kết chất lượng để gia tăng niềm tin cho thực khách</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="(b, idx) in benefits" :key="b.id">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-gray-400 uppercase" x-text="'Cam kết #' + (idx + 1)"></span>
                            <input 
                                type="text" 
                                x-model="b.icon" 
                                class="w-10 h-10 text-center rounded-xl bg-white border border-gray-200 text-base shadow-2xs font-bold"
                                title="Icon emoji"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-gray-700">Tiêu đề cam kết</label>
                            <input 
                                type="text" 
                                x-model="b.title" 
                                class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-black text-gray-900 outline-none focus:border-red-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-gray-700">Mô tả cam kết</label>
                            <textarea 
                                x-model="b.description" 
                                rows="2" 
                                class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-medium text-gray-700 outline-none focus:border-red-500"
                            ></textarea>
                        </div>

                        <button 
                            type="button" 
                            @click="saveBenefit(b)"
                            class="w-full py-2 rounded-xl bg-gray-900 hover:bg-black text-white font-bold text-xs transition-colors cursor-pointer"
                        >
                            💾 Lưu Cam Kết Này
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- TAB 5: 💬 ĐÁNH GIÁ KHÁCH HÀNG (TESTIMONIALS) -->
    <div x-show="activeTab === 'testimonials'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>💬</span>
                        <span>Đánh Giá Khách Hàng (Customer Reviews)</span>
                    </h3>
                    <p class="text-xs text-gray-400">Các feedback khen ngon chân thực xuất hiện ở phần chân website</p>
                </div>
                <button 
                    type="button" 
                    @click="openCreateReviewModal()"
                    class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                >
                    + Thêm Đánh Giá Mới
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="r in testimonials" :key="r.id">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 shadow-2xs space-y-2 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-red-100 text-red-600 font-black text-xs flex items-center justify-center" x-text="r.customer_name.charAt(0)"></span>
                                <div>
                                    <h4 class="font-black text-xs text-gray-900" x-text="r.customer_name"></h4>
                                    <span class="text-[10px] text-gray-400" x-text="r.location || 'Hà Nội'"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <button 
                                    type="button" 
                                    @click="openEditReviewModal(r)"
                                    class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-600 text-xs font-bold cursor-pointer"
                                >
                                    ✏️
                                </button>
                                <button 
                                    type="button" 
                                    @click="deleteReview(r)"
                                    class="p-1.5 rounded-lg hover:bg-rose-100 text-rose-600 text-xs font-bold cursor-pointer"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>

                        <div class="text-amber-500 text-xs tracking-widest">
                            ★★★★★
                        </div>

                        <p class="text-xs text-gray-700 italic" x-text="'“' + r.content + '”'"></p>

                        <div class="pt-1 border-t border-gray-200/60 text-[10px] text-gray-500 font-semibold" x-text="'Món yêu thích: ' + r.favorite_dish"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- TAB 6: 🎁 POPUP KHUYẾN MÃI -->
    <div x-show="activeTab === 'popup'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.content.index', ['tab' => 'popup']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🎁</span>
                            <span>Banner Popup Khuyến Mãi (Tự Động Hiện Khi Khách Vào Web)</span>
                        </h3>
                        <p class="text-xs text-gray-400">Bật/tắt popup chào mừng, ưu đãi freeship hoặc tặng món</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    <!-- Form Cấu hình Popup -->
                    <div class="md:col-span-7 space-y-4 text-xs">
                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Trạng thái hiển thị Popup</label>
                            <select 
                                name="popup_enabled" 
                                x-model="popupState.enabled"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none cursor-pointer"
                            >
                                <option value="1">🟢 Đang BẬT hiển thị Popup</option>
                                <option value="0">⚫ TẮT Popup (Không hiển thị)</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Tiêu đề Popup</label>
                            <input 
                                type="text" 
                                name="popup_title" 
                                x-model="popupState.title"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Nội dung ưu đãi chi tiết</label>
                            <textarea 
                                name="popup_description" 
                                x-model="popupState.description"
                                rows="3" 
                                class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">Chữ nút bấm (CTA)</label>
                                <input 
                                    type="text" 
                                    name="popup_cta_text" 
                                    x-model="popupState.ctaText"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                                >
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">Link chuyển hướng khi click</label>
                                <input 
                                    type="text" 
                                    name="popup_cta_url" 
                                    x-model="popupState.ctaUrl"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                                >
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Ảnh Banner Popup (Tùy chọn tải lên)</label>
                            <input 
                                type="file" 
                                name="popup_banner_file" 
                                @change="handlePopupFile"
                                accept="image/*"
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium cursor-pointer"
                            >
                        </div>
                    </div>

                    <!-- Live Preview Popup Box -->
                    <div class="md:col-span-5 bg-gray-100 p-4 rounded-2xl border border-gray-200 space-y-3">
                        <span class="text-[10px] font-black uppercase text-gray-500 block tracking-wider">👁️ Mô phỏng Popup hiển thị:</span>
                        
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200 space-y-3 p-4 text-center">
                            <template x-if="popupState.filePreview || popupState.imageUrl">
                                <img :src="popupState.filePreview || popupState.imageUrl" class="w-full h-36 object-cover rounded-xl mb-2">
                            </template>
                            <h4 class="font-black text-sm text-gray-900" x-text="popupState.title"></h4>
                            <p class="text-xs text-gray-600 leading-relaxed" x-text="popupState.description"></p>
                            <button type="button" class="w-full py-2.5 rounded-xl bg-red-600 text-white font-black text-xs shadow-sm" x-text="popupState.ctaText"></button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cấu Hình Popup
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 7: 🔻 CHÂN TRANG & LIÊN HỆ (FOOTER & SOCIAL) -->
    <div x-show="activeTab === 'footer'" class="space-y-6" x-cloak>
        
        <!-- 🔴 BẢN XEM TRƯỚC TRỰC QUAN CHÂN TRANG (LIVE PREVIEW FOOTER) -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                <div>
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-pulse"></span>
                        <span>Mô Phỏng Trực Quan Chân Trang (Live Preview Footer)</span>
                    </h3>
                    <p class="text-xs text-gray-500">Đối chiếu trực quan từng cột trên Website theo thời gian thực khi bạn chỉnh sửa dữ liệu bên dưới</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Live Preview
                    </span>
                </div>
            </div>

            <!-- Khung mô phỏng Footer Dark Mode chuẩn Website -->
            <div class="rounded-2xl bg-[#141416] text-white p-6 sm:p-8 border border-gray-800 shadow-xl overflow-hidden relative">
                <!-- Mô phỏng 2 nút Chat tròn nổi góc trái -->
                <div class="absolute bottom-4 left-4 z-10 flex flex-col gap-2 pointer-events-none opacity-90 hidden sm:flex">
                    <div class="w-8 h-8 rounded-full bg-[#0084FF] text-white flex items-center justify-center text-xs font-bold shadow-lg" title="Nút Messenger">💬</div>
                    <div class="w-8 h-8 rounded-full bg-[#0068FF] text-white flex items-center justify-center text-[10px] font-black shadow-lg" title="Nút Zalo">Zalo</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 text-xs">
                    <!-- CỘT 1: THƯƠNG HIỆU & MẠNG XÃ HỘI -->
                    <div class="lg:col-span-4 space-y-3 relative p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <span class="inline-block px-2 py-0.5 rounded bg-red-950/80 text-red-400 font-extrabold text-[10px] uppercase tracking-wider border border-red-800/40">
                            📍 Cột 1: Thương Hiệu & MXH
                        </span>
                        <div class="flex items-center gap-2.5 pt-1">
                            <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-sm shadow-md">🍗</div>
                            <div>
                                <span class="text-lg font-black tracking-tight text-white block leading-none" x-text="footerState.siteName || 'GAO'">GAO</span>
                                <span class="text-[9px] font-bold text-gray-400 tracking-wider" x-text="footerState.siteTagline || 'GÀ SỐT & CƠM'">GÀ SỐT & CƠM</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed line-clamp-3" x-text="footerState.footerDescription || 'Thương hiệu Gà Sốt & Cơm chuẩn vị...'"></p>
                        
                        <div class="flex items-center gap-2 pt-1">
                            <span class="px-2 py-0.5 rounded-full bg-gray-800 text-[10px] font-bold text-gray-300 border border-gray-700">FB</span>
                            <span class="px-2 py-0.5 rounded-full bg-gray-800 text-[10px] font-bold text-gray-300 border border-gray-700">IG</span>
                            <span class="px-2 py-0.5 rounded-full bg-gray-800 text-[10px] font-bold text-gray-300 border border-gray-700">TT</span>
                        </div>
                    </div>

                    <!-- CỘT 2: THỰC ĐƠN (ĐỒNG BỘ TỰ ĐỘNG) -->
                    <div class="lg:col-span-2 space-y-2.5 p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <span class="inline-block px-2 py-0.5 rounded bg-amber-950/80 text-amber-400 font-extrabold text-[10px] uppercase tracking-wider border border-amber-800/40">
                            🍗 Cột 2: Thực Đơn
                        </span>
                        <ul class="space-y-1.5 text-xs text-gray-400 font-medium">
                            @forelse($categories ?? [] as $cat)
                                <li class="truncate hover:text-red-400">• {{ $cat->name }}</li>
                            @empty
                                <li>• Cơm Gà</li>
                                <li>• Gà Chiên Sốt</li>
                                <li>• Combo</li>
                                <li>• Ăn Kèm & Đồ Uống</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- CỘT 3: CAM KẾT & DỊCH VỤ -->
                    <div class="lg:col-span-3 space-y-2.5 p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <span class="inline-block px-2 py-0.5 rounded bg-emerald-950/80 text-emerald-400 font-extrabold text-[10px] uppercase tracking-wider border border-emerald-800/40">
                            🛡️ Cột 3: Cam Kết & Dịch Vụ
                        </span>
                        <h5 class="text-xs font-bold text-white uppercase tracking-wider" x-text="footerState.servicesTitle || 'Cam Kết & Dịch Vụ'"></h5>
                        <ul class="space-y-1.5 text-xs text-gray-400 font-medium">
                            <li x-show="footerState.showTracking !== '0'" class="text-orange-400 font-bold flex items-center gap-1.5">
                                <span>🔍</span>
                                <span x-text="footerState.trackingText || 'Tra Cứu Đơn Hàng'"></span>
                            </li>
                            <template x-for="(srv, idx) in (footerState.servicesList || '').split('\n').map(s => s.trim()).filter(Boolean)" :key="idx">
                                <li class="flex items-center gap-1.5">
                                    <span x-text="srv"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- CỘT 4: THÔNG TIN LIÊN HỆ -->
                    <div class="lg:col-span-3 space-y-2.5 p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <span class="inline-block px-2 py-0.5 rounded bg-blue-950/80 text-blue-400 font-extrabold text-[10px] uppercase tracking-wider border border-blue-800/40">
                            📍 Cột 4: Thông Tin Liên Hệ
                        </span>
                        <ul class="space-y-2 text-xs text-gray-400 font-medium">
                            <li class="flex items-start gap-1.5">
                                <span class="text-red-500">📍</span>
                                <span class="leading-snug line-clamp-2" x-text="footerState.storeAddress || 'Địa chỉ phục vụ...'"></span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-red-500">📞</span>
                                <span>Hotline: <strong class="text-white" x-text="footerState.hotline || '0988.868.GAO'"></strong></span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-red-500">⏰</span>
                                <span>Nhận đơn: <strong class="text-white" x-text="(footerState.kitchenOpenTime || '09:30') + ' – ' + (footerState.kitchenCloseTime || '22:30')"></strong></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Đáy chân trang -->
                <div class="mt-6 pt-4 border-t border-gray-800 text-[11px] text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-1.5 py-0.5 rounded bg-purple-950/80 text-purple-400 font-extrabold text-[9px] uppercase border border-purple-800/40">Đáy Trang</span>
                        <span x-text="footerState.copyright || '© 2026 GAO - All rights reserved.'"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span x-text="footerState.footerSlogan || 'Thực đơn gà sốt đậm vị chuẩn Hà Nội'"></span>
                        <span>•</span>
                        <span>🔒 Quản Trị</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM CÀI ĐẶT FOOTER CHIA THEO CẤU TRÚC 4 CỘT -->
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.content.index', ['tab' => 'footer']) }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- 🟥 CỘT 1: THƯƠNG HIỆU, GIỚI THIỆU & MẠNG XÃ HỘI -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                            <h4 class="font-bold text-sm text-gray-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-red-100 text-red-600 flex items-center justify-center text-xs font-black">1</span>
                                <span>Cột 1: Giới Thiệu Thương Hiệu & Mạng Xã Hội</span>
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-[11px] font-bold border border-red-200">Cột 1 Footer</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="space-y-1.5">
                                <label class="block font-bold text-gray-700">Mô tả giới thiệu thương hiệu ở Footer</label>
                                <textarea 
                                    name="footer_description" 
                                    rows="3"
                                    x-model="footerState.footerDescription"
                                    placeholder="Thương hiệu Gà Sốt & Cơm chuẩn vị..."
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                ></textarea>
                            </div>

                            <div class="pt-2 border-t border-gray-100">
                                <span class="font-bold text-gray-800 block mb-2">🌐 Liên kết Mạng Xã Hội (Hiển thị icon FB, IG, TT)</span>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Fanpage Facebook</label>
                                        <input 
                                            type="text" 
                                            name="social_facebook" 
                                            x-model="footerState.facebookUrl"
                                            placeholder="https://facebook.com/..."
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                        >
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kênh TikTok</label>
                                        <input 
                                            type="text" 
                                            name="social_tiktok" 
                                            x-model="footerState.tiktokUrl"
                                            placeholder="https://tiktok.com/@..."
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                        >
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kênh Instagram (Tùy chọn)</label>
                                        <input 
                                            type="text" 
                                            name="social_instagram" 
                                            x-model="footerState.instagramUrl"
                                            placeholder="https://instagram.com/..."
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🟧 CỘT 2: THỰC ĐƠN (ĐỒNG BỘ TỰ ĐỘNG) -->
                <div class="bg-gradient-to-br from-amber-50/60 via-white to-orange-50/40 rounded-2xl border border-amber-200/80 shadow-xs p-5 sm:p-6 space-y-4 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="border-b border-amber-200/60 pb-3 flex items-center justify-between">
                            <h4 class="font-bold text-sm text-gray-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-black">2</span>
                                <span>Cột 2: Danh Mục Thực Đơn</span>
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold border border-amber-300">Tự Động 100%</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="bg-white p-4 rounded-xl border border-amber-200 space-y-2.5 shadow-2xs">
                                <div class="flex items-center gap-2 text-amber-900 font-bold">
                                    <span>⚙️</span>
                                    <span>Cơ chế đồng bộ tự động:</span>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Cột này được hệ thống tự động quét danh sách <strong>Danh mục món ăn đang kích hoạt</strong> (*Cơm Gà, Gà Chiên Sốt, Combo, Ăn Kèm, Đồ Uống...*).
                                </p>
                                <p class="text-gray-600 leading-relaxed">
                                    Mỗi mục là 1 đường link dẫn trực tiếp khách tới món thuộc danh mục đó.
                                </p>
                            </div>

                            <!-- Preview danh sách danh mục hiện tại -->
                            <div class="bg-white/80 p-3.5 rounded-xl border border-gray-200 space-y-2">
                                <span class="font-bold text-gray-700 block">Danh mục đang hiển thị trên Footer:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($categories ?? [] as $cat)
                                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 font-semibold text-gray-700 border border-gray-200">
                                            🍗 {{ $cat->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 italic">Chưa có danh mục nào</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-amber-200/60">
                        <a 
                            href="{{ route('admin.categories.index') }}" 
                            class="w-full py-2.5 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-95 text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-sm transition-all"
                        >
                            <span>👉 Quản Lý & Đổi Tên Danh Mục Món</span>
                        </a>
                    </div>
                </div>

                <!-- 🟩 CỘT 3: CAM KẾT & DỊCH VỤ -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                            <h4 class="font-bold text-sm text-gray-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-black">3</span>
                                <span>Cột 3: Cam Kết & Dịch Vụ</span>
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">Cột 3 Footer</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="space-y-1.5">
                                <label class="block font-bold text-gray-700">Tiêu đề cột (Column Title)</label>
                                <input 
                                    type="text" 
                                    name="footer_services_title" 
                                    x-model="footerState.servicesTitle"
                                    placeholder="Cam Kết & Dịch Vụ"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                >
                            </div>

                            <div class="space-y-1.5">
                                <label class="block font-bold text-gray-700">Nút Tra cứu đơn hàng</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <select 
                                        name="footer_show_tracking" 
                                        x-model="footerState.showTracking"
                                        class="px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-800 focus:bg-white focus:border-red-500 outline-none cursor-pointer"
                                    >
                                        <option value="1">Hiển thị</option>
                                        <option value="0">Ẩn nút</option>
                                    </select>
                                    <input 
                                        type="text" 
                                        name="footer_tracking_text" 
                                        x-model="footerState.trackingText"
                                        placeholder="Tra Cứu Đơn Hàng"
                                        class="col-span-2 px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-orange-600 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                    >
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="block font-bold text-gray-700">Danh sách các cam kết dịch vụ</label>
                                    <span class="text-[11px] text-gray-400 italic">Mỗi dòng 1 cam kết</span>
                                </div>
                                <textarea 
                                    name="footer_services_list" 
                                    rows="4"
                                    x-model="footerState.servicesList"
                                    placeholder="🛵 Freeship 3km từ 100k&#10;🔥 Giao nhanh nóng hổi 25–40p&#10;🍗 100% Gà tươi chiên giòn&#10;✨ Đảm bảo vệ sinh ATTP"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none font-mono text-xs leading-relaxed transition-colors"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🔵 CỘT 4: THÔNG TIN LIÊN HỆ & GIỜ BẾP HOẠT ĐỘNG -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                            <h4 class="font-bold text-sm text-gray-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-black">4</span>
                                <span>Cột 4: Thông Tin Liên Hệ & Giờ Mở Bếp</span>
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold border border-blue-200">Cột 4 Footer</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="space-y-1.5">
                                <label class="block font-bold text-gray-700">📍 Địa chỉ phục vụ chính (Store Address)</label>
                                <input 
                                    type="text" 
                                    name="store_address" 
                                    x-model="footerState.storeAddress"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                >
                            </div>

                            <div class="space-y-1.5">
                                <label class="block font-bold text-gray-700">📞 Số điện thoại Hotline đặt món</label>
                                <input 
                                    type="text" 
                                    name="hotline" 
                                    x-model="footerState.hotline"
                                    placeholder="0988.868.GAO"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-red-600 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                >
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div class="space-y-1.5">
                                    <label class="block font-bold text-gray-700">⏰ Giờ mở nhận đơn</label>
                                    <input 
                                        type="time" 
                                        name="kitchen_open_time" 
                                        x-model="footerState.kitchenOpenTime"
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-800 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                    >
                                </div>

                                <div class="space-y-1.5">
                                    <label class="block font-bold text-gray-700">🌙 Giờ đóng nhận đơn</label>
                                    <input 
                                        type="time" 
                                        name="kitchen_close_time" 
                                        x-model="footerState.kitchenCloseTime"
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-800 focus:bg-white focus:border-red-500 outline-none transition-colors"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🟪 ĐÁY CHÂN TRANG: BẢN QUYỀN & KHẨU HIỆU -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <h4 class="font-bold text-sm text-gray-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-xs font-black">5</span>
                            <span>Đáy Chân Trang: Bản Quyền & Khẩu Hiệu Slogan</span>
                        </h4>
                        <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 text-[11px] font-bold border border-purple-200">Dòng Dưới Cùng</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div class="space-y-1.5">
                            <label class="block font-bold text-gray-700">Bản quyền Website (Copyright)</label>
                            <input 
                                type="text" 
                                name="copyright" 
                                x-model="footerState.copyright"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-colors"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="block font-bold text-gray-700">Khẩu hiệu / Slogan Chân Trang</label>
                            <input 
                                type="text" 
                                name="footer_slogan" 
                                x-model="footerState.footerSlogan"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-colors"
                            >
                        </div>
                    </div>
                </div>

                <!-- 💬 NÚT CHAT TRÒN NỔI GÓC TRÁI (ZALO & MESSENGER) -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <h4 class="font-bold text-sm text-gray-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-black">6</span>
                            <span>2 Nút Chat Tròn Nổi Góc Trái (Zalo & Messenger)</span>
                        </h4>
                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold border border-blue-200">Góc Màn Hình</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div class="space-y-1.5">
                            <label class="block font-bold text-blue-600 flex items-center gap-1">
                                <span>💬</span>
                                <span>Link Chat Zalo</span>
                            </label>
                            <input 
                                type="text" 
                                name="contact_zalo_url" 
                                x-model="footerState.zaloUrl"
                                placeholder="https://zalo.me/0973797151"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none transition-colors"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="block font-bold text-blue-600 flex items-center gap-1">
                                <span>💬</span>
                                <span>Link Chat Messenger</span>
                            </label>
                            <input 
                                type="text" 
                                name="contact_messenger_url" 
                                x-model="footerState.messengerUrl"
                                placeholder="https://m.me/luuhoang.it"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none transition-colors"
                            >
                        </div>
                    </div>
                </div>

            </div>

            <!-- THANH HÀNH ĐỘNG: NÚT LƯU CÀI ĐẶT FOOTER -->
            <div class="sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-4 rounded-2xl border border-gray-200/90 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Toàn bộ thay đổi sẽ được áp dụng trực tiếp lên Chân Trang website ngay sau khi lưu.</span>
                </div>
                <button 
                    type="submit" 
                    class="w-full sm:w-auto px-8 py-3 rounded-xl bg-red-600 hover:bg-red-700 active:scale-95 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-600/25 transition-all cursor-pointer flex items-center justify-center gap-2"
                >
                    <span>💾</span>
                    <span>Lưu Cài Đặt Chân Trang (Footer)</span>
                </button>
            </div>
        </form>
    </div>

    <!-- MODAL DRAWER TẠO / SỬA ĐÁNH GIÁ (TESTIMONIAL) -->
    <div 
        x-show="showReviewDrawer" 
        class="fixed inset-0 z-50 overflow-hidden" 
        x-cloak
    >
        <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" @click="showReviewDrawer = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white p-6 shadow-2xl space-y-4 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="font-black text-sm text-gray-900 uppercase" x-text="isEditingReview ? '✏️ Sửa Đánh Giá' : '➕ Thêm Đánh Giá Mới'"></h3>
                        <button @click="showReviewDrawer = false" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer">✕</button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Tên khách hàng <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="reviewForm.customer_name" 
                                placeholder="VD: Hoàng Yến" 
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Khu vực / Quận</label>
                            <input 
                                type="text" 
                                x-model="reviewForm.location" 
                                placeholder="VD: Cầu Giấy, Hà Nội" 
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-900 outline-none focus:border-red-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Món ăn yêu thích</label>
                            <input 
                                type="text" 
                                x-model="reviewForm.favorite_dish" 
                                placeholder="VD: Cơm Gà Sốt Cay Hàn" 
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Nội dung đánh giá <span class="text-red-500">*</span></label>
                            <textarea 
                                x-model="reviewForm.content" 
                                rows="4" 
                                placeholder="Cảm nhận thực tế của khách hàng..." 
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-900 outline-none focus:border-red-500"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="showReviewDrawer = false" 
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs cursor-pointer"
                    >
                        Hủy
                    </button>
                    <button 
                        type="button" 
                        @click="saveReview()" 
                        class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase shadow-sm cursor-pointer"
                    >
                        Lưu Đánh Giá
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL POPUP: XEM TRƯỚC WEBSITE TRỰC TIẾP (LIVE IFRAME PREVIEW) -->
    <div 
        x-show="showWebPreviewModal" 
        class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-2 sm:p-4 bg-black/70 backdrop-blur-sm"
        x-cloak
    >
        <div 
            class="bg-gray-900 rounded-3xl w-full h-full max-w-7xl max-h-[92vh] flex flex-col shadow-2xl border border-gray-700 overflow-hidden"
            @click.away="showWebPreviewModal = false"
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
function contentAdminData() {
    return {
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'hero',
        toastMessage: '',
        showReviewDrawer: false,
        isEditingReview: false,
        activeMenuReviewId: null,
        showWebPreviewModal: false,
        previewDevice: 'desktop',

        setTab(tab) {
            this.activeTab = tab;
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        },

        showToast(msg) {
            this.toastMessage = msg;
            setTimeout(() => { this.toastMessage = ''; }, 3500);
        },

        refreshPreviewIframe() {
            const iframe = document.getElementById('webPreviewIframe');
            if (iframe) {
                iframe.src = iframe.src;
            }
        },

        // 1. Header State
        headerState: {
            topNotification: {!! json_encode($settings['top_notification'] ?? 'Ưu đãi hôm nay: Freeship bán kính 3km cho đơn hàng từ 100k!') !!},
            hotline: {!! json_encode($settings['hotline'] ?? '0988.868.GAO') !!},
            siteName: {!! json_encode($settings['site_name'] ?? 'GAO') !!},
            siteTagline: {!! json_encode($settings['site_tagline'] ?? 'GÀ SỐT & CƠM') !!},
            locationShort: {!! json_encode($settings['location_short'] ?? 'Hà Nội') !!},
            locationBadge: {!! json_encode($settings['location_badge'] ?? 'Hà Nội (3–5km)') !!},
            ctaText: {!! json_encode($settings['header_cta_text'] ?? 'Đặt món') !!},
            ctaUrl: {!! json_encode($settings['header_cta_url'] ?? '/menu') !!}
        },

        // 2. Hero State
        hero: {
            badge: {!! json_encode($hero->badge ?? '✦ GÀ CHIÊN + SỐT ĐẬM VỊ') !!},
            title: {!! json_encode($hero->title ?? 'GÀ GIÒN.') !!},
            title_highlight: {!! json_encode($hero->title_highlight ?? 'SỐT ĐẬM.') !!},
            subtitle: {!! json_encode($hero->subtitle ?? 'Gà nóng giòn, phủ sốt nguyên bản. Giao tận nơi tại Hà Nội trong 25–40 phút.') !!},
            cta_primary_text: {!! json_encode($hero->cta_primary_text ?? '🍗 ĐẶT MÓN NGAY') !!},
            cta_primary_url: {!! json_encode($hero->cta_primary_url ?? '/menu') !!},
            cta_secondary_text: {!! json_encode($hero->cta_secondary_text ?? '🔥 XEM 4 VỊ SỐT') !!},
            cta_secondary_url: {!! json_encode($hero->cta_secondary_url ?? '/menu') !!},
            delivery_time: {!! json_encode($hero->delivery_time ?? 'Giao 25–40p') !!},
            hot_status: {!! json_encode($hero->hot_status ?? 'Luôn nóng giòn') !!},
            rating: {!! json_encode($hero->rating ?? 'Đánh giá 4.9/5') !!},
            price: {{ (float)($hero->price ?? 49000) }},
            floating_badge: {!! json_encode($hero->floating_badge ?? '🔥 MÓN MỚI RA MẮT • ĐẶT NGAY') !!},
            image: {!! json_encode($hero->image ?? '') !!},
            imagePreview: {!! json_encode(!empty($hero->image) ? (str_starts_with($hero->image, 'http') ? $hero->image : asset($hero->image)) : 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=1000&q=85') !!},
            filePreview: null
        },

        handleHeroFile(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.hero.filePreview = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        // 3. Sections Heading & Toggle State
        sectionsState: {
            saucesEnabled: {!! json_encode($settings['section_sauces_enabled'] ?? '1') !!},
            saucesBadge: {!! json_encode($settings['home_sauces_badge'] ?? 'TINH HOA HƯƠNG VỊ') !!},
            saucesTitle: {!! json_encode($settings['home_sauces_title'] ?? '4 VỊ SỐT ĐẶC TRƯNG TẠI GAO') !!},
            saucesSubtitle: {!! json_encode($settings['home_sauces_subtitle'] ?? 'Sốt thủ công nguyên bản, sánh mịn thơm lừng phủ đẫm trên từng miếng gà giòn rụm.') !!},
            
            popularEnabled: {!! json_encode($settings['section_popular_enabled'] ?? '1') !!},
            popularBadge: {!! json_encode($settings['home_popular_badge'] ?? 'MÓN ĐƯỢC GỌI NHIỀU') !!},
            popularTitle: {!! json_encode($settings['home_popular_title'] ?? 'Thực Đơn Đậm Vị Được Yêu Thích Nhất') !!},
            popularSubtitle: {!! json_encode($settings['home_popular_subtitle'] ?? 'Những món gà sốt và cơm gà được đặt nhiều nhất mỗi ngày tại GAO') !!},

            combosEnabled: {!! json_encode($settings['section_combos_enabled'] ?? '1') !!},
            combosTitle: {!! json_encode($settings['home_combos_title'] ?? 'ĂN COMBO, LỜI HƠN') !!},
            combosSubtitle: {!! json_encode($settings['home_combos_subtitle'] ?? 'Tiết kiệm tới 55.000đ khi đi theo nhóm, ăn no nê cùng bạn bè & người thân.') !!},

            benefitsEnabled: {!! json_encode($settings['section_benefits_enabled'] ?? '1') !!},

            testimonialsEnabled: {!! json_encode($settings['section_testimonials_enabled'] ?? '1') !!},
            testimonialsTitle: {!! json_encode($settings['home_testimonials_title'] ?? 'KHÁCH ĂN NÓI GÌ?') !!},
            testimonialsSubtitle: {!! json_encode($settings['home_testimonials_subtitle'] ?? 'Hơn 10.000+ bữa ăn ngon đã được giao đến tay khách hàng tại Hà Nội') !!}
        },

        // 4. Popup State
        popupState: {
            enabled: {!! json_encode($settings['popup_enabled'] ?? '0') !!},
            title: {!! json_encode($settings['popup_title'] ?? '🎉 Ưu Đãi Đặc Biệt Hôm Nay!') !!},
            description: {!! json_encode($settings['popup_description'] ?? 'Tặng ngay 01 hũ sốt đặc trưng hoặc Freeship 3km cho đơn hàng từ 100k hôm nay. Đặt ngay để nhận ưu đãi!') !!},
            ctaText: {!! json_encode($settings['popup_cta_text'] ?? 'Xem Thực Đơn Đặt Ngay →') !!},
            ctaUrl: {!! json_encode($settings['popup_cta_url'] ?? '/menu') !!},
            imageUrl: {!! json_encode(!empty($settings['popup_banner_image']) ? (str_starts_with($settings['popup_banner_image'], 'http') ? $settings['popup_banner_image'] : asset($settings['popup_banner_image'])) : '') !!},
            filePreview: null
        },

        // 5. Benefits State (3 cam kết)
        benefits: {!! json_encode($benefits->map(fn($b) => [
            'id' => $b->id,
            'icon' => $b->icon ?: '⚡',
            'title' => $b->title,
            'description' => $b->description,
            'saved_title' => $b->title,
            'saved_description' => $b->description,
            'saved_icon' => $b->icon ?: '⚡',
        ])) !!},

        // 6. Testimonials State (Đánh giá)
        testimonials: {!! json_encode($testimonials->map(fn($t) => [
            'id' => $t->id,
            'customer_name' => $t->customer_name,
            'location' => $t->location,
            'favorite_dish' => $t->favorite_dish ?: 'Cơm Gà Sốt Cay Hàn',
            'rating' => (int)$t->rating,
            'content' => $t->comment ?: $t->content,
        ])) !!},

        // 7. Footer State (Chân trang & Liên hệ)
        footerState: {
            siteName: {!! json_encode($settings['site_name'] ?? 'GAO') !!},
            siteTagline: {!! json_encode($settings['site_tagline'] ?? 'GÀ SỐT & CƠM') !!},
            footerDescription: {!! json_encode($settings['footer_description'] ?? 'Thương hiệu Gà Sốt & Cơm chuẩn vị tại Hà Nội. Gà giòn rụm, đẫm sốt đậm đà, phục vụ nóng hổi tận tay khách hàng trong bán kính 3–5km.') !!},
            storeAddress: {!! json_encode($settings['store_address'] ?? 'Hà Nội: Đống Đa, Cầu Giấy, Hoàn Kiếm, Hai Bà Trưng, Ba Đình, Thanh Xuân.') !!},
            hotline: {!! json_encode($settings['hotline'] ?? '0988.868.GAO') !!},
            kitchenOpenTime: {!! json_encode($settings['kitchen_open_time'] ?? '09:30') !!},
            kitchenCloseTime: {!! json_encode($settings['kitchen_close_time'] ?? '22:30') !!},
            servicesTitle: {!! json_encode($settings['footer_services_title'] ?? 'Cam Kết & Dịch Vụ') !!},
            showTracking: {!! json_encode($settings['footer_show_tracking'] ?? '1') !!},
            trackingText: {!! json_encode($settings['footer_tracking_text'] ?? 'Tra Cứu Đơn Hàng') !!},
            servicesList: {!! json_encode($settings['footer_services_list'] ?? "🛵 Freeship 3km từ 100k\n🔥 Giao nhanh nóng hổi 25–40p\n🍗 100% Gà tươi chiên giòn\n✨ Đảm bảo vệ sinh ATTP") !!},
            footerSlogan: {!! json_encode($settings['footer_slogan'] ?? 'Thực đơn gà sốt đậm vị chuẩn Hà Nội') !!},
            copyright: {!! json_encode($settings['copyright'] ?? '© 2026 GAO - Gà Sốt & Cơm Hà Nội. All rights reserved.') !!},
            zaloUrl: {!! json_encode($settings['contact_zalo_url'] ?? 'https://zalo.me/0973797151') !!},
            messengerUrl: {!! json_encode($settings['contact_messenger_url'] ?? 'https://m.me/luuhoang.it') !!},
            facebookUrl: {!! json_encode($settings['social_facebook'] ?? 'https://facebook.com') !!},
            tiktokUrl: {!! json_encode($settings['social_tiktok'] ?? 'https://tiktok.com') !!},
            instagramUrl: {!! json_encode($settings['social_instagram'] ?? 'https://instagram.com') !!},
        },

        reviewForm: {
            id: null,
            customer_name: '',
            location: '',
            favorite_dish: 'Cơm Gà Sốt Cay Hàn',
            rating: 5,
            content: ''
        },

        async saveBenefit(b) {
            try {
                const res = await fetch(`/admin/content/benefit/${b.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        title: b.title,
                        description: b.description,
                        icon: b.icon
                    })
                });
                const data = await res.json();
                if (data.success) {
                    b.saved_title = b.title;
                    b.saved_description = b.description;
                    b.saved_icon = b.icon;
                    this.showToast(`Đã lưu cam kết '${b.title}'!`);
                }
            } catch (e) {
                alert('Không thể lưu cam kết.');
            }
        },

        handlePopupFile(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.popupState.filePreview = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        openCreateReviewModal() {
            this.isEditingReview = false;
            this.reviewForm = {
                id: null,
                customer_name: '',
                location: '',
                favorite_dish: 'Cơm Gà Sốt Cay Hàn',
                rating: 5,
                content: ''
            };
            this.showReviewDrawer = true;
        },

        openEditReviewModal(r) {
            this.isEditingReview = true;
            this.reviewForm = {
                id: r.id,
                customer_name: r.customer_name,
                location: r.location,
                favorite_dish: r.favorite_dish,
                rating: r.rating,
                content: r.content
            };
            this.activeMenuReviewId = null;
            this.showReviewDrawer = true;
        },

        async saveReview() {
            if (!this.reviewForm.customer_name.trim() || !this.reviewForm.content.trim()) {
                alert('Vui lòng nhập tên khách hàng và nội dung đánh giá.');
                return;
            }

            try {
                if (this.isEditingReview) {
                    const res = await fetch(`/admin/content/testimonial/${this.reviewForm.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.reviewForm)
                    });
                    const data = await res.json();
                    if (data.success) {
                        const idx = this.testimonials.findIndex(t => t.id === this.reviewForm.id);
                        if (idx !== -1) {
                            this.testimonials[idx] = { ...this.reviewForm };
                        }
                        this.showReviewDrawer = false;
                        this.showToast('Đã cập nhật đánh giá thành công!');
                    }
                } else {
                    const res = await fetch('{{ route('admin.content.testimonial.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.reviewForm)
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.testimonials.push({
                            id: data.testimonial.id,
                            customer_name: data.testimonial.customer_name,
                            location: data.testimonial.location,
                            favorite_dish: data.testimonial.favorite_dish,
                            rating: data.testimonial.rating,
                            content: data.testimonial.content
                        });
                        this.showReviewDrawer = false;
                        this.showToast('Đã thêm đánh giá mới thành công!');
                    }
                }
            } catch (e) {
                alert('Không thể lưu đánh giá.');
            }
        },

        async deleteReview(r) {
            this.activeMenuReviewId = null;
            if (!confirm(`Xoá đánh giá của '${r.customer_name}'?`)) return;

            try {
                const res = await fetch(`/admin/content/testimonial/${r.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.testimonials = this.testimonials.filter(t => t.id !== r.id);
                    this.showToast('Đã xoá đánh giá!');
                }
            } catch (e) {
                alert('Không thể xoá đánh giá.');
            }
        }
    };
}
</script>
@endsection
