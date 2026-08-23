@extends('layouts.app')

@section('title', 'Tra Cứu Đơn Hàng | GAO - Gà Sốt & Cơm Hà Nội')

@section('content')
<div class="min-h-[calc(100vh-340px)] bg-[#FAF6F0] py-6 sm:py-10 pb-24 md:pb-14 flex flex-col justify-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-6 w-full">

        <!-- 1. TIÊU ĐỀ SECTION (GỌN GÀNG, SANG TRỌNG) -->
        <div class="text-center space-y-1">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 tracking-tight">
                TRA CỨU <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500">ĐƠN HÀNG</span>
            </h1>
            <p class="text-xs sm:text-sm text-gray-500">
                Theo dõi tiến trình làm món & giao hàng theo thời gian thực
            </p>
        </div>

        <!-- 2. FORM TRA CỨU DẠNG PILL (CAO CẤP, TINH TẾ, ĐỒNG NHẤT) -->
        <div 
            x-data="{ isSubmitting: false }" 
            class="max-w-lg mx-auto w-full"
        >
            <form 
                action="{{ route('order.tracking') }}" 
                method="GET" 
                @submit="isSubmitting = true"
                class="flex items-center bg-white p-1.5 rounded-full shadow-md shadow-orange-950/5 border border-orange-200/70 transition-all focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/10"
            >
                <div class="relative flex-1 flex items-center pl-3.5">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $query }}"
                        placeholder="Nhập SĐT hoặc Mã đơn (GAO-...)" 
                        class="w-full h-11 pl-2.5 pr-2 bg-transparent text-gray-900 placeholder-gray-400 font-semibold text-xs sm:text-sm outline-none"
                        required
                        autofocus
                    >
                </div>
                
                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="h-11 px-5 sm:px-6 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-sm hover:scale-[1.02] active:scale-95 transition-all duration-150 cursor-pointer flex items-center justify-center gap-1.5 shrink-0 disabled:opacity-75 disabled:cursor-not-allowed"
                >
                    <template x-if="!isSubmitting">
                        <span class="flex items-center gap-1">
                            <span>Tra Cứu</span>
                            <span>→</span>
                        </span>
                    </template>
                    <template x-if="isSubmitting">
                        <span class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Đang tìm...</span>
                        </span>
                    </template>
                </button>
            </form>

            <!-- Gợi ý tra cứu 1 dòng siêu gọn -->
            <div class="flex items-center justify-center gap-2 pt-2 text-[11px] text-gray-500 font-medium">
                <span>Gợi ý:</span>
                <a href="{{ route('order.tracking', ['q' => '0973797151']) }}" class="text-orange-700 bg-orange-100/70 px-2 py-0.2 rounded-md font-bold font-mono hover:bg-orange-200/70 transition-colors">0973797151</a>
                <span class="text-gray-300">•</span>
                <span>Mã đơn: <strong class="text-gray-700 font-mono">GAO-xxxxxx</strong></span>
            </div>
        </div>

        @if($hasSearched)
            @if($activeOrder)
                <!-- 3. TRẠNG THÁI: TÌM THẤY ĐƠN HÀNG (ACTIVE ORDER CARD) -->
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-lg border border-gray-100 overflow-hidden space-y-4">
                    
                    <!-- 3.1 Header Tóm Tắt Đơn Hàng -->
                    <div class="bg-gradient-to-r from-gray-900 via-gray-900 to-gray-800 text-white px-4 sm:px-6 py-3.5 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="text-[11px] uppercase font-bold tracking-widest text-gray-400">MÃ ĐƠN:</span>
                            <span class="px-2 py-0.5 rounded-md text-xs sm:text-sm font-black bg-white/10 text-orange-400 border border-white/15 font-mono">
                                {{ $activeOrder->order_code }}
                            </span>
                            <span class="text-gray-400 text-xs">•</span>
                            <span class="text-[11px] sm:text-xs text-gray-300">
                                {{ $activeOrder->created_at ? $activeOrder->created_at->format('H:i - d/m/Y') : 'Vừa xong' }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black border {{ $activeOrder->status_color }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                <span>{{ $activeOrder->status_label }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- 3.2 Thông Báo Trạng Thái Thông Minh (Smart Status Banner) -->
                    @if($activeOrder->order_status === 'completed')
                        <div class="mx-4 sm:mx-6 p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">🎉</span>
                                <span class="font-bold text-emerald-800">Đơn hàng đã được giao thành công! Chúc bạn ngon miệng!</span>
                            </div>
                            <a href="{{ route('menu') }}" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shrink-0">
                                Đặt lại món
                            </a>
                        </div>
                    @elseif($activeOrder->order_status === 'cancelled')
                        <div class="mx-4 sm:mx-6 p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2.5">
                            <span class="text-lg">❌</span>
                            <span class="font-bold">Đơn hàng này đã được huỷ. Hotline hỗ trợ: {{ $settings['hotline'] ?? '0988.868.GAO' }}</span>
                        </div>
                    @elseif($activeOrder->order_status === 'delivering' || $activeOrder->order_status === 'shipping')
                        <div class="mx-4 sm:mx-6 p-3 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-center gap-2.5">
                            <span class="text-lg animate-bounce">🛵</span>
                            <span class="font-bold text-blue-800">Shipper đang trên đường giao món. Dự kiến đến bạn trong 15–25 phút nữa!</span>
                        </div>
                    @elseif($activeOrder->order_status === 'preparing' || $activeOrder->order_status === 'processing')
                        <div class="mx-4 sm:mx-6 p-3 rounded-2xl bg-orange-50 border border-orange-200 text-orange-900 text-xs flex items-center gap-2.5">
                            <span class="text-lg">🔥</span>
                            <span class="font-bold text-orange-800">Bếp đang chiên gà giòn & phủ sốt nóng hổi. Sẽ giao cho shipper ngay!</span>
                        </div>
                    @endif

                    <!-- 3.3 TIMELINE 5 BƯỚC (THANH TIẾN TRÌNH RÕ RÀNG VỚI ĐƯỜNG NỐI) -->
                    @if($activeOrder->order_status !== 'cancelled')
                        @php
                            $currentStep = $activeOrder->status_step;
                            $steps = [
                                1 => ['title' => 'Đã đặt', 'icon' => '📝'],
                                2 => ['title' => 'Xác nhận', 'icon' => '📞'],
                                3 => ['title' => 'Chuẩn bị', 'icon' => '🔥'],
                                4 => ['title' => 'Đang giao', 'icon' => '🛵'],
                                5 => ['title' => 'Đã giao', 'icon' => '✅'],
                            ];
                        @endphp

                        <div class="px-4 sm:px-6">
                            <div class="bg-gray-50/90 p-3 sm:p-4 rounded-2xl border border-gray-100">
                                <div class="grid grid-cols-5 gap-1 text-center relative">
                                    @foreach($steps as $stepNumber => $stepInfo)
                                        @php
                                            $isPassed = $currentStep >= $stepNumber;
                                            $isCurrent = $currentStep === $stepNumber;
                                        @endphp
                                        <div class="flex flex-col items-center">
                                            
                                            <!-- Circle Step Indicator -->
                                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs mb-1 transition-all {{ $isCurrent ? 'bg-red-600 text-white ring-4 ring-red-500/20 shadow-md font-black scale-105' : ($isPassed ? 'bg-emerald-500 text-white font-bold' : 'bg-gray-200 text-gray-400') }}">
                                                @if($isPassed && !$isCurrent)
                                                    <span class="text-[11px]">✓</span>
                                                @else
                                                    <span class="text-[11px] sm:text-xs">{{ $stepInfo['icon'] }}</span>
                                                @endif
                                            </div>

                                            <!-- Step Title -->
                                            <h4 class="text-[10px] sm:text-xs font-black {{ $isCurrent ? 'text-red-600' : ($isPassed ? 'text-gray-900' : 'text-gray-400') }} leading-tight">
                                                {{ $stepInfo['title'] }}
                                            </h4>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- 3.4 BỐ CỤC 2 CỘT THÔNG TIN CÂN ĐỐI (GIAO HÀNG & MÓN ĂN) -->
                    <div class="px-4 sm:px-6 grid grid-cols-1 md:grid-cols-12 gap-4">
                        
                        <!-- CỘT TRÁI (5 PHẦN): THÔNG TIN GIAO HÀNG & THANH TOÁN -->
                        <div class="md:col-span-5 space-y-3">
                            
                            <!-- Box Giao hàng -->
                            <div class="bg-[#FAF6F0] p-3.5 rounded-2xl border border-orange-100/80 space-y-2 text-xs">
                                <h4 class="font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5 text-[11px]">
                                    <span>📍</span>
                                    <span>GIAO HÀNG TẬN NƠI</span>
                                </h4>
                                <div class="space-y-1.5 pt-0.5 text-gray-700">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Người nhận:</span>
                                        <span class="font-bold text-gray-900">{{ $activeOrder->customer_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Số điện thoại:</span>
                                        <span class="font-bold text-red-600 font-mono">{{ $activeOrder->customer_phone }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Khu vực:</span>
                                        <span class="font-semibold text-gray-900">{{ $activeOrder->district }}</span>
                                    </div>
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="text-gray-500 shrink-0">Địa chỉ:</span>
                                        <span class="font-semibold text-gray-900 text-right">{{ $activeOrder->address }}</span>
                                    </div>
                                    @if($activeOrder->driver_note)
                                        <div class="pt-1.5 border-t border-orange-200/50 flex justify-between items-start gap-2">
                                            <span class="text-gray-500 shrink-0">Ghi chú:</span>
                                            <span class="text-orange-900 italic text-right font-medium">"{{ $activeOrder->driver_note }}"</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Box Thanh toán -->
                            <div class="bg-[#FAF6F0] p-3.5 rounded-2xl border border-orange-100/80 space-y-2 text-xs">
                                <h4 class="font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5 text-[11px]">
                                    <span>💳</span>
                                    <span>THANH TOÁN</span>
                                </h4>
                                <div class="space-y-1.5 pt-0.5 text-gray-700">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Hình thức:</span>
                                        <span class="font-bold text-gray-900">{{ $activeOrder->payment_method_label }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Trạng thái:</span>
                                        <span class="px-2 py-0.2 rounded-full font-bold text-[10px] {{ $activeOrder->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $activeOrder->payment_status === 'paid' ? 'Đã thanh toán' : 'Thu COD khi nhận' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center pt-1 border-t border-orange-200/50">
                                        <span class="text-gray-500">Thời gian giao:</span>
                                        <span class="font-bold text-emerald-700">25 – 40 phút</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- CỘT PHẢI (7 PHẦN): DANH SÁCH MÓN ĂN & TỔNG TIỀN -->
                        <div class="md:col-span-7 space-y-3">
                            
                            <div class="border border-gray-200/80 rounded-2xl overflow-hidden divide-y divide-gray-100">
                                <div class="bg-gray-50 px-3.5 py-2 flex items-center justify-between text-[11px] font-black text-gray-500 uppercase tracking-wider">
                                    <span>MÓN ĐÃ ĐẶT ({{ $activeOrder->items->count() }})</span>
                                    <span>THÀNH TIỀN</span>
                                </div>

                                @foreach($activeOrder->items as $item)
                                    <div class="p-3 flex items-center justify-between gap-3 hover:bg-gray-50/50 transition-colors">
                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-gray-900 text-xs sm:text-sm">{{ $item->product_name }}</span>
                                                <span class="px-1.5 py-0.2 rounded bg-gray-100 text-gray-700 text-[10px] font-bold font-mono">x{{ $item->quantity }}</span>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-1 text-[11px] text-gray-500">
                                                @if($item->sauce)
                                                    <span class="bg-red-50 text-red-700 px-1.5 py-0.2 rounded font-medium">🌶️ {{ $item->sauce }}</span>
                                                @endif
                                                @if($item->spice_level)
                                                    <span class="bg-orange-50 text-orange-700 px-1.5 py-0.2 rounded font-medium">{{ $item->spice_level }}</span>
                                                @endif
                                                @if(!empty($item->toppings) && is_array($item->toppings))
                                                    <span>Topping: <strong>{{ implode(', ', $item->toppings) }}</strong></span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span class="font-black text-gray-900 text-xs sm:text-sm">
                                                {{ number_format((float) ($item->total_item_price ?: ($item->price * $item->quantity)), 0, ',', '.') }}đ
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Tính tiền & Tổng thanh toán -->
                            <div class="bg-gray-50/90 rounded-2xl p-3 space-y-1 border border-gray-200/80 text-xs">
                                <div class="flex justify-between text-gray-600">
                                    <span>Tạm tính:</span>
                                    <span class="font-bold text-gray-900">{{ number_format((float) $activeOrder->subtotal, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Phí giao hàng:</span>
                                    @if((float) $activeOrder->shipping_fee === 0.0)
                                        <span class="font-bold text-emerald-600 uppercase">Miễn phí (Freeship 3km)</span>
                                    @else
                                        <span class="font-bold text-gray-900">{{ number_format((float) $activeOrder->shipping_fee, 0, ',', '.') }}đ</span>
                                    @endif
                                </div>
                                @if((float) $activeOrder->discount > 0)
                                    <div class="flex justify-between text-emerald-600">
                                        <span>Giảm giá:</span>
                                        <span class="font-bold">-{{ number_format((float) $activeOrder->discount, 0, ',', '.') }}đ</span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center text-sm pt-1.5 border-t border-gray-200">
                                    <span class="font-black text-gray-900 uppercase">TỔNG CỘNG:</span>
                                    <span class="font-black text-red-600 text-base sm:text-lg">{{ number_format((float) $activeOrder->total_amount, 0, ',', '.') }}đ</span>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- 3.5 NÚT HÀNH ĐỘNG (CTA CHÍNH VÀ PHỤ) -->
                    <div class="bg-gray-50 px-4 sm:px-6 py-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-2.5">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <a 
                                href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline'] ?? '0988868000') }}" 
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-800 text-xs font-bold border border-gray-200 transition-colors"
                            >
                                <span>📞</span>
                                <span>Gọi Bếp</span>
                            </a>
                            <a 
                                href="{{ $settings['contact_zalo_url'] ?? 'https://zalo.me/0973797151' }}" 
                                target="_blank" 
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-gray-100 text-[#0068FF] text-xs font-bold border border-gray-200 transition-colors"
                            >
                                <span>💬</span>
                                <span>Chat Zalo</span>
                            </a>
                        </div>

                        <a 
                            href="{{ route('menu') }}" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-xs font-black uppercase tracking-wider shadow-sm hover:scale-[1.01] active:scale-95 transition-all"
                        >
                            <span>🍗 Đặt thêm món mới</span>
                            <span>→</span>
                        </a>
                    </div>

                </div>

                <!-- 4. LỊCH SỬ CÁC ĐƠN KHÁC (GỌN GÀNG, KHÔNG TRANH CHẤP) -->
                @if($recentOrders->isNotEmpty())
                    <div class="space-y-2 pt-1">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider">
                            LỊCH SỬ ĐƠN HÀNG KHÁC CỦA SĐT NÀY ({{ $recentOrders->count() }})
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($recentOrders as $prevOrder)
                                <a 
                                    href="{{ route('order.tracking', ['code' => $prevOrder->order_code]) }}" 
                                    class="bg-white p-2.5 rounded-xl border border-gray-200/70 hover:border-red-300 transition-all flex items-center justify-between gap-2 group"
                                >
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-xs text-gray-900 group-hover:text-red-600 transition-colors font-mono">{{ $prevOrder->order_code }}</span>
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold border {{ $prevOrder->status_color }}">
                                                {{ $prevOrder->status_label }}
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-gray-400">{{ $prevOrder->created_at ? $prevOrder->created_at->format('H:i - d/m/Y') : '' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-xs text-gray-900">{{ number_format((float) $prevOrder->total_amount, 0, ',', '.') }}đ</span>
                                        <span class="block text-[10px] text-red-600 font-bold group-hover:underline">Xem →</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            @else
                <!-- 5. TRẠNG THÁI: KHÔNG TÌM THẤY ĐƠN HÀNG (EMPTY STATE) -->
                <div class="bg-white rounded-2xl p-5 sm:p-7 text-center shadow-sm border border-gray-200/80 max-w-sm mx-auto space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xl">
                        🔍
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-sm sm:text-base font-black text-gray-900">Không tìm thấy đơn hàng!</h3>
                        <p class="text-xs text-gray-500">
                            Không có kết quả khớp: <strong class="text-red-600 font-mono">"{{ $query }}"</strong>
                        </p>
                    </div>

                    <div class="flex items-center justify-center gap-2 pt-1">
                        <a 
                            href="{{ route('order.tracking') }}" 
                            class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold transition-colors"
                        >
                            Thử lại
                        </a>
                        <a 
                            href="{{ route('menu') }}" 
                            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-wider transition-colors"
                        >
                            Xem thực đơn
                        </a>
                    </div>
                </div>
            @endif

        @else
            <!-- 6. 3 CARD CAM KẾT COMPACT DẠNG MINI BAR -->
            <div class="space-y-3 pt-1">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                    
                    <div class="bg-white p-3 rounded-2xl border border-gray-200/70 shadow-xs flex items-center gap-2.5">
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-sm">
                            ⏱️
                        </div>
                        <div>
                            <h4 class="font-black text-gray-900 text-xs">Giao Hàng 25–40 Phút</h4>
                            <p class="text-[11px] text-gray-500">Chiên giòn & giao nóng hổi.</p>
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded-2xl border border-gray-200/70 shadow-xs flex items-center gap-2.5">
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                            🛵
                        </div>
                        <div>
                            <h4 class="font-black text-gray-900 text-xs">Freeship Đơn Từ 100K</h4>
                            <p class="text-[11px] text-gray-500">Bán kính 3km nội thành Hà Nội.</p>
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded-2xl border border-gray-200/70 shadow-xs flex items-center gap-2.5">
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                            🍗
                        </div>
                        <div>
                            <h4 class="font-black text-gray-900 text-xs">Cam Kết Nóng Giòn</h4>
                            <p class="text-[11px] text-gray-500">Đóng hộp giữ nhiệt chuẩn vị.</p>
                        </div>
                    </div>

                </div>

                <!-- Secondary Menu CTA Link -->
                <div class="text-center pt-0.5">
                    <a 
                        href="{{ route('menu') }}" 
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-white hover:bg-orange-50/80 text-gray-600 hover:text-red-600 font-bold text-xs border border-gray-200/80 shadow-xs transition-all"
                    >
                        <span>🍗 Bạn chưa đặt món hôm nay?</span>
                        <span class="text-red-600 font-black underline">Khám Phá Thực Đơn Ngay →</span>
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
