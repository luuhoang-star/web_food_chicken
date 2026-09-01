@extends('layouts.admin')

@section('title', 'Cài Đặt Vận Hành Quán & Hệ Thống')
@section('page_title', '⚙️ Cài Đặt Vận Hành Quán & Hệ Thống')

@section('content')
<div 
    class="max-w-5xl space-y-6 pb-24" 
    x-data="{
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'store',
        
        setTab(tab) {
            this.activeTab = tab;
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        },

        // Realtime state cho VietQR Preview
        bankState: {
            bankCode: '{{ $settings['bank_code'] ?? 'MB' }}',
            bankName: '{{ addslashes($settings['bank_name'] ?? 'MB Bank (Quân Đội)') }}',
            accountNumber: '{{ $settings['bank_account_number'] ?? '0988888888' }}',
            accountHolder: '{{ addslashes($settings['bank_account_holder'] ?? 'GAO CHICKEN HA NOI') }}',
            prefix: '{{ addslashes($settings['bank_transfer_prefix'] ?? 'HUBBY') }}'
        },

        // Realtime state cho SEO Preview
        seoState: {
            ogImageUrl: '{{ !empty($settings['og_image']) ? (str_starts_with($settings['og_image'], 'http') ? $settings['og_image'] : asset($settings['og_image'])) : '' }}',
            ogFilePreview: null,
            faviconUrl: '{{ !empty($settings['favicon_url']) ? (str_starts_with($settings['favicon_url'], 'http') ? $settings['favicon_url'] : asset($settings['favicon_url'])) : '' }}',
            favFilePreview: null,
            metaTitle: '{{ addslashes($settings['meta_title'] ?? 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị') }}',
            metaDesc: '{{ addslashes($settings['meta_description'] ?? 'Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội. Giao nhanh 25-40 phút.') }}'
        },

        handleOgFile(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.seoState.ogFilePreview = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        handleFaviconFile(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.seoState.favFilePreview = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }"
>

    <!-- 1. HEADER (TITLE) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>⚙️ Cài Đặt Vận Hành Quán & Hệ Thống</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">
                Cấu hình vận hành kỹ thuật: Bếp nhận đơn, Phí ship giao hàng, Ngân hàng VietQR, Báo chuông Telegram & SEO.
            </p>
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
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🔍 5. SEO & Google</span>
        </button>

    </div>

    <!-- TAB 1: 🏢 BẾP & GIỜ MỞ CỬA -->
    <div x-show="activeTab === 'store'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            
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

            <!-- Form Cài đặt giờ mở cửa -->
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <input type="hidden" name="_redirect_to" value="{{ route('admin.settings.index', ['tab' => 'store']) }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Giờ Bắt Đầu Nhận Đơn (Buổi Sáng)</label>
                        <input 
                            type="time" 
                            name="kitchen_open_time" 
                            value="{{ $settings['kitchen_open_time'] ?? '09:30' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Giờ Ngừng Nhận Đơn (Buổi Tối)</label>
                        <input 
                            type="time" 
                            name="kitchen_close_time" 
                            value="{{ $settings['kitchen_close_time'] ?? '22:30' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="block font-bold text-gray-700">Ghi chú giờ cao điểm / Lời nhắc khách hàng</label>
                        <input 
                            type="text" 
                            name="rush_hour_note" 
                            value="{{ $settings['rush_hour_note'] ?? 'Giờ trưa 11:30 - 13:00 quán có thể giao chậm hơn 10 phút, mong quý khách thông cảm!' }}"
                            placeholder="Thông báo cho khách khi quán đông..." 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
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

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🛵</span>
                        <span>Cấu Hình Bán Kính Giao Hàng & Phí Ship</span>
                    </h3>
                    <p class="text-xs text-gray-400">Tự động tính phí vận chuyển khi khách chọn địa chỉ hoặc khoảng cách km</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Khoảng cách cơ bản (km)</label>
                        <input 
                            type="number" 
                            name="shipping_base_distance" 
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
                            value="{{ $settings['delivery_time_estimate'] ?? '25 - 40 phút' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
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
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>💳</span>
                        <span>Cấu Hình Tài Khoản Ngân Hàng VietQR (Tự Động Tạo Mã Chuyển Khoản)</span>
                    </h3>
                    <p class="text-xs text-gray-400">Khách quét mã chuyển khoản sẽ tự điền sẵn Số tiền & Mã đơn hàng</p>
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

                <div class="flex justify-end pt-3 border-t border-gray-100">
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

                <div class="flex justify-end pt-3 border-t border-gray-100">
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

    <!-- TAB 5: 🔍 SEO & GOOGLE -->
    <div x-show="activeTab === 'seo'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.settings.index', ['tab' => 'seo']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🔍</span>
                        <span>Cấu Hình SEO Google & Chia Sẻ Mạng Xã Hội (Facebook, Zalo)</span>
                    </h3>
                    <p class="text-xs text-gray-400">Tiêu đề, mô tả và hình ảnh hiển thị khi tìm kiếm trên Google hoặc gửi link qua chat</p>
                </div>

                <!-- Google Search Snippet Preview -->
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 space-y-1">
                    <span class="text-[10px] font-black uppercase text-gray-500 block tracking-wider">👁️ Mô phỏng kết quả tìm kiếm trên Google:</span>
                    <div class="text-blue-700 font-semibold text-sm hover:underline cursor-pointer" x-text="seoState.metaTitle"></div>
                    <div class="text-emerald-700 text-[11px] font-mono">https://gaochicken.vn/</div>
                    <div class="text-gray-600 text-xs leading-relaxed" x-text="seoState.metaDesc"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1 md:col-span-2">
                        <label class="block font-bold text-gray-700">Tiêu đề Trang Web (Meta Title)</label>
                        <input 
                            type="text" 
                            name="meta_title" 
                            x-model="seoState.metaTitle"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="block font-bold text-gray-700">Đoạn Mô Tả Tìm Kiếm (Meta Description)</label>
                        <textarea 
                            name="meta_description" 
                            x-model="seoState.metaDesc"
                            rows="3" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        ></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Ảnh đại diện khi chia sẻ Link (OG Image)</label>
                        <input 
                            type="file" 
                            name="og_image_file" 
                            @change="handleOgFile"
                            accept="image/*"
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium cursor-pointer"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Icon nhỏ trên tab trình duyệt (Favicon)</label>
                        <input 
                            type="file" 
                            name="favicon_file" 
                            @change="handleFaviconFile"
                            accept="image/*"
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium cursor-pointer"
                        >
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cấu Hình SEO
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
