@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng & Giao Nhận')
@section('page_title', '📋 Quản Lý Đơn Hàng & Giao Nhận')

@section('content')
<div 
    class="space-y-4" 
    x-data="{
        selectedDrawerOrder: null,
        activePrintOrder: null,
        copyToast: '',
        lastOrderId: {{ $latestOrderId }},
        newOrdersCount: 0,
        audioEnabled: true,
        orderStatuses: {},

        init() {
            // Polling check đơn mới mỗi 15 giây
            setInterval(() => {
                this.checkNewOrders();
            }, 15000);
        },

        async checkNewOrders() {
            if (this.lastOrderId <= 0) return;
            try {
                const res = await fetch(`/admin/orders/check-new?last_order_id=${this.lastOrderId}`);
                const data = await res.json();
                if (data.success && data.has_new) {
                    this.newOrdersCount = data.new_count;
                    if (this.audioEnabled) {
                        this.playNotificationSound();
                    }
                }
            } catch (e) {}
        },

        playNotificationSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15); // A5
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.4);
            } catch(e) {}
        },

        showToast(msg) {
            this.copyToast = msg;
            setTimeout(() => { this.copyToast = ''; }, 3500);
        },

        openDrawer(order) {
            if (!order) return;
            this.selectedDrawerOrder = order;
        },

        openPrintModal(order) {
            if (!order) return;
            this.activePrintOrder = order;
        },

        printDirect(order) {
            if (!order) return;
            this.activePrintOrder = order;
            setTimeout(() => {
                window.print();
            }, 100);
        },

        copyShipperInfo(order) {
            if (!order) return;
            const storeAddr = '{{ $settings['store_address'] ?? 'Quán GAO - Gà Sốt & Cơm Hà Nội' }}';
            const storePhone = '{{ $settings['hotline'] ?? '0988.868.GAO' }}';
            const codText = order.is_paid ? '0 ₫ (ĐÃ CHUYỂN KHOẢN TRƯỚC)' : order.total + ' (THU HỘ COD)';
            
            const itemsTxt = order.items.map(i => i.qty + 'x ' + i.name + (i.sauce ? ' (Sốt ' + i.sauce + ')' : '')).join('; ');

            const text = `📦 ĐƠN GIAO HÀNG GAO [#${order.code}]
📍 Lấy hàng: ${storeAddr} (SĐT Bếp: ${storePhone})
📍 Giao tới: ${order.address}, ${order.district}
👤 Khách: ${order.name} - SĐT: ${order.phone}
🍗 Món: ${itemsTxt}
💵 Tiền thu khách: ${codText}
📝 Ghi chú: ${order.driver_note || 'Giao nóng hổi'}`;

            const successMsg = `Đã sao chép đơn #${order.code} để gửi Shipper / Grab / AhaMove!`;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    this.showToast(successMsg);
                }).catch(() => {
                    this.fallbackCopy(text, successMsg);
                });
            } else {
                this.fallbackCopy(text, successMsg);
            }
        },

        fallbackCopy(text, successMsg) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.top = '-9999px';
            textArea.style.left = '-9999px';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                this.showToast(successMsg);
            } catch (err) {
                prompt('Sao chép thông tin đơn gửi Shipper:', text);
            }
            document.body.removeChild(textArea);
        },

        async updateOrderStatus(orderId, newStatus, orderCode) {
            try {
                const res = await fetch(`/admin/orders/${orderId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_status: newStatus })
                });
                const data = await res.json();
                if (data.success) {
                    this.orderStatuses[orderId] = {
                        status: data.order_status,
                        label: data.status_label,
                        color: data.status_color,
                        is_paid: data.is_paid
                    };

                    if (this.selectedDrawerOrder && this.selectedDrawerOrder.id === orderId) {
                        this.selectedDrawerOrder.status = data.order_status;
                        this.selectedDrawerOrder.status_label = data.status_label;
                        this.selectedDrawerOrder.status_color = data.status_color;
                        this.selectedDrawerOrder.is_paid = data.is_paid;
                    }

                    this.showToast(`Đã chuyển đơn #${orderCode || data.order_code} sang: ${data.status_label}!`);
                } else {
                    alert('Không thể cập nhật trạng thái đơn.');
                }
            } catch (e) {
                alert('Lỗi kết nối khi cập nhật đơn hàng.');
            }
        },

        openDrawer(orderData) {
            this.selectedDrawerOrder = orderData;
        },

        openPrintModal(orderData) {
            this.activePrintOrder = orderData;
        }
    }"
    @keydown.window.escape="selectedDrawerOrder = null; activePrintOrder = null"
    @keydown.window.p="if (selectedDrawerOrder && !activePrintOrder) { openPrintModal(selectedDrawerOrder); }"
    @keydown.window.c="if (selectedDrawerOrder && !activePrintOrder) { copyShipperInfo(selectedDrawerOrder); }"
>

    <!-- BANNER BÁO ĐƠN HÀNG MỚI THỜI GIAN THỰC -->
    <div 
        x-show="newOrdersCount > 0" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="bg-gradient-to-r from-red-600 to-amber-600 text-white p-3 sm:p-4 rounded-2xl shadow-lg flex items-center justify-between gap-3 text-xs font-bold animate-pulse"
        x-cloak
    >
        <div class="flex items-center gap-2">
            <span class="text-lg">🔔</span>
            <span>Có <span class="font-black text-sm underline" x-text="newOrdersCount"></span> đơn hàng mới vừa đặt!</span>
        </div>

        <a 
            href="{{ route('admin.orders.index') }}" 
            class="px-4 py-1.5 rounded-xl bg-white text-red-700 hover:bg-gray-100 font-black shadow-xs transition-transform active:scale-95"
        >
            ↻ Tải lại trang ngay
        </a>
    </div>

    <!-- FLOATING COPY TOAST NOTIFICATION -->
    <div 
        x-show="copyToast" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-20 md:bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-2.5 rounded-2xl shadow-2xl border border-gray-700 flex items-center gap-2.5 text-xs font-bold"
        x-cloak
    >
        <span class="text-sm">📋</span>
        <span x-text="copyToast"></span>
    </div>

    <!-- 1. BỘ LỌC TRẠNG THÁI (STATUS TABS), LỌC THEO NGÀY & TÌM KIẾM TINH GỌN -->
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Status Tabs With Clean Counter Badges -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0 text-xs font-bold scrollbar-none">
                @php
                    $tabs = [
                        'all' => ['label' => 'Tất cả', 'key' => 'all'],
                        'pending' => ['label' => 'Chờ xử lý', 'key' => 'pending'],
                        'preparing' => ['label' => 'Đang làm', 'key' => 'preparing'],
                        'delivering' => ['label' => 'Đang giao', 'key' => 'delivering'],
                        'completed' => ['label' => 'Đã giao', 'key' => 'completed'],
                        'cancelled' => ['label' => 'Đã huỷ', 'key' => 'cancelled'],
                    ];
                @endphp
                @foreach($tabs as $tabKey => $tabInfo)
                    @php
                        $count = $statusCounts[$tabInfo['key']] ?? 0;
                        $isActive = ($currentStatus === $tabKey);
                    @endphp
                    <a 
                        href="{{ route('admin.orders.index', ['status' => $tabKey, 'date' => $currentDate, 'q' => $search]) }}" 
                        class="px-3 py-1.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 {{ $isActive ? 'bg-gray-900 text-white shadow-xs font-black' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    >
                        <span>{{ $tabInfo['label'] }}</span>
                        @if($count > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $isActive ? 'bg-white text-gray-900' : ($tabKey === 'pending' ? 'bg-red-600 text-white' : ($tabKey === 'preparing' ? 'bg-orange-500 text-white' : 'bg-gray-300 text-gray-800')) }}">
                                {{ $count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            <!-- Date Filter Pills (Hôm nay / 7 ngày / Tất cả) -->
            <div class="flex items-center gap-1.5 self-start lg:self-center">
                <span class="text-[11px] font-bold text-gray-400">Thời gian:</span>
                
                <a 
                    href="{{ route('admin.orders.index', ['status' => $currentStatus, 'date' => 'today', 'q' => $search]) }}" 
                    class="px-2.5 py-1 rounded-xl text-xs font-bold transition-colors {{ $currentDate === 'today' ? 'bg-red-50 text-red-700 border border-red-200 font-black' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}"
                >
                    📅 Hôm nay
                </a>

                <a 
                    href="{{ route('admin.orders.index', ['status' => $currentStatus, 'date' => '7days', 'q' => $search]) }}" 
                    class="px-2.5 py-1 rounded-xl text-xs font-bold transition-colors {{ $currentDate === '7days' ? 'bg-red-50 text-red-700 border border-red-200 font-black' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}"
                >
                    7 ngày
                </a>

                <a 
                    href="{{ route('admin.orders.index', ['status' => $currentStatus, 'date' => 'all', 'q' => $search]) }}" 
                    class="px-2.5 py-1 rounded-xl text-xs font-bold transition-colors {{ ($currentDate === 'all' || empty($currentDate)) ? 'bg-gray-900 text-white font-black' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}"
                >
                    Tất cả
                </a>
            </div>

        </div>

        <!-- Search Box & Export Excel Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-gray-100">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center gap-1.5 flex-1 max-w-md">
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                <input type="hidden" name="date" value="{{ $currentDate }}">
                <div class="relative w-full">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $search }}"
                        placeholder="🔍 Tìm theo mã đơn, Tên khách, SĐT, Địa chỉ..." 
                        class="w-full pl-3 pr-7 py-1.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:border-red-500 outline-none transition-colors"
                    >
                    @if($search)
                        <a href="{{ route('admin.orders.index', ['status' => $currentStatus, 'date' => $currentDate]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-600 text-xs font-bold">
                            ✕
                        </a>
                    @endif
                </div>
            </form>

            <div class="flex items-center gap-2 self-end sm:self-center">
                <span class="text-[11px] text-gray-400 font-medium hidden sm:inline">Phím tắt trong Drawer: <strong>C</strong> (Copy), <strong>P</strong> (In), <strong>Esc</strong> (Đóng)</span>

                <a 
                    href="{{ route('admin.orders.export', ['status' => $currentStatus, 'date' => $currentDate, 'q' => $search]) }}" 
                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 shrink-0 cursor-pointer"
                    title="Tải file Excel / CSV danh sách đơn hàng"
                >
                    <span>📥</span>
                    <span>Xuất Excel</span>
                </a>
            </div>
        </div>

    </div>

    <!-- 2. BẢNG DANH SÁCH ĐƠN HÀNG (AJAX STATE TRANSITION, 1-CHẠM) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">Mã Đơn / Giờ</th>
                        <th class="px-4 py-3">Khách Hàng & Giao Hàng</th>
                        <th class="px-4 py-3">Món Bếp Làm</th>
                        <th class="px-4 py-3">Thanh Toán</th>
                        <th class="px-4 py-3 text-right">Tổng Thu</th>
                        <th class="px-4 py-3">Trạng Thái</th>
                        <th class="px-4 py-3 text-right">Thao Tác (1-Chạm)</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    @forelse($orders as $order)
                        @php
                            $formattedTotal = number_format((float) $order->total_amount, 0, ',', '.') . ' ₫';
                            $isPaid = ($order->payment_status === 'paid');
                            
                            // JSON object payload for Detail Drawer
                            $orderPayload = [
                                'id' => $order->id,
                                'code' => $order->order_code,
                                'status' => $order->order_status,
                                'status_label' => $order->status_label,
                                'status_color' => $order->status_color,
                                'time' => $order->created_at ? $order->created_at->format('H:i - d/m/Y') : '',
                                'name' => $order->customer_name,
                                'phone' => $order->customer_phone,
                                'address' => $order->address,
                                'district' => $order->district,
                                'driver_note' => $order->driver_note,
                                'payment_method' => $order->payment_method_label,
                                'is_paid' => $isPaid,
                                'subtotal' => number_format((float) $order->subtotal, 0, ',', '.') . ' ₫',
                                'shipping' => (float) $order->shipping_fee === 0.0 ? '0 ₫ (Freeship)' : number_format((float) $order->shipping_fee, 0, ',', '.') . ' ₫',
                                'discount' => (float) $order->discount > 0 ? '-' . number_format((float) $order->discount, 0, ',', '.') . ' ₫' : null,
                                'total' => $formattedTotal,
                                'items' => $order->items->map(fn($item) => [
                                    'name' => $item->product_name,
                                    'qty' => $item->quantity,
                                    'sauce' => $item->sauce,
                                    'toppings' => $item->toppings,
                                    'price' => number_format((float) ($item->total_item_price ?: ($item->price * $item->quantity)), 0, ',', '.') . ' ₫'
                                ])
                            ];
                        @endphp

                        <tr 
                            class="hover:bg-gray-50/70 transition-colors cursor-pointer"
                            :class="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'pending' ? 'bg-amber-50/30' : ''"
                            @click="openDrawer({{ json_encode($orderPayload) }})"
                        >
                            
                            <!-- 1. Mã đơn & Giờ đặt -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-black text-gray-900 font-mono text-xs block hover:text-red-600 transition-colors">
                                            #{{ $order->order_code }}
                                        </span>
                                        @if($order->order_status === 'pending')
                                            <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-gray-400 block font-normal">
                                        {{ $order->created_at ? $order->created_at->format('H:i - d/m') : '' }}
                                    </span>
                                </div>
                            </td>

                            <!-- 2. Khách hàng & Giao hàng tinh gọn -->
                            <td class="px-4 py-3 min-w-[200px] max-w-[240px]">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <strong class="text-gray-900 font-bold text-xs">{{ $order->customer_name }}</strong>
                                        <span class="text-gray-400 text-[10px]">·</span>
                                        <span class="text-gray-600 font-semibold text-[11px]">{{ $order->district }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px]" @click.stop>
                                        <a href="tel:{{ $order->customer_phone }}" class="text-emerald-700 font-bold font-mono hover:underline">
                                            📞 {{ $order->customer_phone }}
                                        </a>
                                        <a 
                                            href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->address . ', ' . $order->district . ', Hà Nội') }}" 
                                            target="_blank" 
                                            class="text-blue-600 font-bold text-[10px] hover:underline flex items-center gap-0.5"
                                            title="Mở Google Maps chỉ đường"
                                        >
                                            <span>🗺️ Chỉ đường</span>
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <!-- 3. Món bếp làm -->
                            <td class="px-4 py-3 max-w-[260px]">
                                <div class="space-y-1">
                                    @foreach($order->items as $item)
                                        <div class="text-xs leading-tight">
                                            <span class="font-bold text-gray-900">{{ $item->product_name }}</span>
                                            <span class="font-black text-red-600 font-mono">×{{ $item->quantity }}</span>
                                            @if($item->sauce || !empty($item->toppings))
                                                <span class="text-[10px] text-gray-500 font-medium block">
                                                    @if($item->sauce) Sốt {{ $item->sauce }} @endif
                                                    @if($item->sauce && !empty($item->toppings)) · @endif
                                                    @if(!empty($item->toppings)) {{ implode(', ', (array) $item->toppings) }} @endif
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- 4. Thanh toán -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span 
                                    class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold"
                                    :class="(orderStatuses[{{ $order->id }}]?.is_paid ?? {{ $isPaid ? 'true' : 'false' }}) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                    x-text="(orderStatuses[{{ $order->id }}]?.is_paid ?? {{ $isPaid ? 'true' : 'false' }}) ? '💳 Đã CK' : '💵 Thu COD'"
                                >
                                    {{ $isPaid ? '💳 Đã CK' : '💵 Thu COD' }}
                                </span>
                            </td>

                            <!-- 5. Tổng thu -->
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span class="font-black text-red-600 text-xs block">
                                    {{ $formattedTotal }}
                                </span>
                                <span class="text-[10px] text-gray-400">
                                    {{ (float) $order->shipping_fee === 0.0 ? 'Freeship' : 'Ship ' . number_format((float) $order->shipping_fee, 0, ',', '.') . '₫' }}
                                </span>
                            </td>

                            <!-- 6. Trạng thái (Live Dynamic) -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span 
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold border"
                                    :class="orderStatuses[{{ $order->id }}]?.color || '{{ $order->status_color }}'"
                                    x-text="orderStatuses[{{ $order->id }}]?.label || '{{ $order->status_label }}'"
                                >
                                    {{ $order->status_label }}
                                </span>
                            </td>

                            <!-- 7. THAO TÁC: 1 PRIMARY CTA & NÚT IN NHANH (AJAX 1-CHẠM) -->
                            <td class="px-4 py-3 whitespace-nowrap text-right" @click.stop>
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <button 
                                        type="button" 
                                        @click="openPrintModal({{ json_encode($orderPayload) }})"
                                        class="p-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors cursor-pointer"
                                        title="In phiếu bếp K80"
                                    >
                                        🖨️
                                    </button>

                                    <template x-if="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'pending'">
                                        <button 
                                            type="button" 
                                            @click="updateOrderStatus({{ $order->id }}, 'preparing', '{{ $order->order_code }}')"
                                            class="px-3 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-black text-xs shadow-xs transition-transform active:scale-95 cursor-pointer"
                                            title="Xác nhận và bắt đầu làm món"
                                        >
                                            🍳 Nhận & Làm món
                                        </button>
                                    </template>

                                    <template x-if="['confirmed', 'preparing', 'processing'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}')">
                                        <button 
                                            type="button" 
                                            @click="updateOrderStatus({{ $order->id }}, 'delivering', '{{ $order->order_code }}')"
                                            class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs shadow-xs transition-transform active:scale-95 cursor-pointer"
                                            title="Đóng gói và đi giao"
                                        >
                                            📦 Đóng gói & Giao
                                        </button>
                                    </template>

                                    <template x-if="['delivering', 'shipping'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}')">
                                        <button 
                                            type="button" 
                                            @click="updateOrderStatus({{ $order->id }}, 'completed', '{{ $order->order_code }}')"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-xs transition-transform active:scale-95 cursor-pointer"
                                            title="Xác nhận đã giao tận tay khách và thu tiền"
                                        >
                                            ✅ Giao xong
                                        </button>
                                    </template>

                                    <template x-if="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'completed'">
                                        <span class="text-xs font-bold text-emerald-700 inline-block py-1">✓ Hoàn tất</span>
                                    </template>

                                    <template x-if="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'cancelled'">
                                        <span class="text-xs font-bold text-gray-400 inline-block py-1">✕ Đã huỷ</span>
                                    </template>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400 text-xs">
                                Không tìm thấy đơn hàng nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-3.5 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif

    </div>

    <!-- 3. ORDER DETAIL DRAWER (SLIDE-OVER PANEL TRƯỢT TỪ BÊN PHẢI KHI CLICK ĐƠN) -->
    <div 
        x-show="selectedDrawerOrder" 
        class="fixed inset-0 z-50 overflow-hidden" 
        x-cloak
    >
        <!-- Backdrop -->
        <div 
            x-show="selectedDrawerOrder"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-xs" 
            @click="selectedDrawerOrder = null"
        ></div>

        <!-- Drawer Content -->
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div 
                x-show="selectedDrawerOrder"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between"
            >
                
                <!-- Drawer Header -->
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-base text-gray-900 font-mono" x-text="'#' + selectedDrawerOrder?.code"></h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border" :class="selectedDrawerOrder?.status_color" x-text="selectedDrawerOrder?.status_label"></span>
                        </div>
                        <span class="text-[10px] text-gray-400" x-text="selectedDrawerOrder?.time"></span>
                    </div>

                    <button 
                        @click="selectedDrawerOrder = null" 
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold flex items-center justify-center transition-colors cursor-pointer"
                        title="Đóng (Esc)"
                    >
                        ✕
                    </button>
                </div>

                <!-- Drawer Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4 text-xs">
                    
                    <!-- 1-Touch Action Suite Toolbar -->
                    <div class="p-3 bg-gray-50 rounded-2xl border border-gray-200 space-y-2">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Thao tác 1-chạm (Phím C: Copy, P: In):</span>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <a 
                                :href="'tel:' + selectedDrawerOrder?.phone" 
                                class="py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-center border border-emerald-200 transition-colors flex items-center justify-center gap-1.5"
                            >
                                <span>📞 Gọi khách</span>
                            </a>

                            <a 
                                :href="'https://zalo.me/' + (selectedDrawerOrder?.phone || '').replace(/[^0-9]/g, '')" 
                                target="_blank" 
                                class="py-2 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 font-bold text-center border border-sky-200 transition-colors flex items-center justify-center gap-1.5"
                            >
                                <span>💬 Zalo</span>
                            </a>

                            <a 
                                :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent((selectedDrawerOrder?.address || '') + ', ' + (selectedDrawerOrder?.district || '') + ', Hà Nội')" 
                                target="_blank" 
                                class="py-2 px-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-center border border-blue-200 transition-colors flex items-center justify-center gap-1.5"
                            >
                                <span>🗺️ Chỉ đường</span>
                            </a>

                            <button 
                                type="button" 
                                @click="copyShipperInfo(selectedDrawerOrder)"
                                class="py-2 px-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold text-center border border-purple-200 transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                            >
                                <span>📋 Copy đơn (C)</span>
                            </button>
                        </div>

                        <div class="flex items-center gap-2 pt-1 border-t border-gray-200">
                            <button 
                                type="button" 
                                @click="openPrintModal(selectedDrawerOrder)"
                                class="flex-1 py-2 rounded-xl bg-gray-900 hover:bg-black text-white font-bold text-center transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                            >
                                <span>🖨️ In Phiếu K80 (P)</span>
                            </button>

                            <a 
                                :href="'/tra-cuu-don-hang?code=' + (selectedDrawerOrder?.code || '')" 
                                target="_blank" 
                                class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-center"
                                title="Mở trang tra cứu"
                            >
                                ↗
                            </a>
                        </div>
                    </div>

                    <!-- Customer Delivery Info -->
                    <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-sm text-gray-900 uppercase tracking-wide" x-text="selectedDrawerOrder?.name"></span>
                            <a :href="'tel:' + selectedDrawerOrder?.phone" class="font-mono font-black text-red-600 text-xs bg-red-50 border border-red-200/80 px-2.5 py-0.5 rounded-lg hover:underline" x-text="'📞 ' + selectedDrawerOrder?.phone"></a>
                        </div>
                        
                        <div class="bg-white p-2.5 rounded-xl border border-gray-200/90 shadow-2xs flex items-start gap-2 text-xs">
                            <span class="text-base shrink-0 leading-none">📍</span>
                            <div class="leading-relaxed">
                                <span class="font-black text-gray-900" x-text="selectedDrawerOrder?.address"></span>
                                <span class="text-gray-400 font-bold mx-1">,</span>
                                <strong class="text-gray-800 font-bold" x-text="selectedDrawerOrder?.district"></strong>
                            </div>
                        </div>

                        <template x-if="selectedDrawerOrder?.driver_note && selectedDrawerOrder.driver_note.trim() !== ''">
                            <div class="p-2.5 bg-amber-50 rounded-xl text-orange-950 text-xs italic font-medium border border-amber-200/90 flex items-start gap-1.5">
                                <span class="shrink-0 font-normal">📝</span>
                                <div>
                                    <strong class="font-bold">Ghi chú:</strong> “<span x-text="selectedDrawerOrder.driver_note"></span>”
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Detailed Items Breakdown -->
                    <div class="space-y-2 border-b border-gray-100 pb-3">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Danh sách món đặt:</span>
                        <div class="divide-y divide-gray-100">
                            <template x-for="(it, idx) in selectedDrawerOrder?.items" :key="idx">
                                <div class="py-2 first:pt-0 last:pb-0 space-y-0.5">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-gray-900 text-xs" x-text="it.qty + 'x ' + it.name"></span>
                                        <span class="font-mono font-bold text-gray-700" x-text="it.price"></span>
                                    </div>
                                    <div class="text-[10px] text-gray-500" x-show="it.sauce || (it.toppings && it.toppings.length > 0)">
                                        <span x-show="it.sauce" x-text="'Sốt: ' + it.sauce"></span>
                                        <span x-show="it.sauce && it.toppings && it.toppings.length > 0"> · </span>
                                        <span x-show="it.toppings && it.toppings.length > 0" x-text="'Topping: ' + (Array.isArray(it.toppings) ? it.toppings.join(', ') : it.toppings)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Financial Breakdown -->
                    <div class="space-y-1.5 border-b border-gray-100 pb-3 text-xs">
                        <div class="flex justify-between text-gray-500">
                            <span>Tiền món:</span>
                            <span class="font-mono font-bold text-gray-800" x-text="selectedDrawerOrder?.subtotal"></span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Phí giao hàng:</span>
                            <span class="font-mono font-bold text-gray-800" x-text="selectedDrawerOrder?.shipping"></span>
                        </div>
                        <template x-if="selectedDrawerOrder?.discount">
                            <div class="flex justify-between text-emerald-600 font-bold">
                                <span>Giảm giá Voucher:</span>
                                <span class="font-mono" x-text="selectedDrawerOrder?.discount"></span>
                            </div>
                        </template>
                        <div class="flex justify-between items-center text-sm font-black pt-1 border-t border-gray-100">
                            <span>Tổng thu:</span>
                            <span class="text-red-600 text-base" x-text="selectedDrawerOrder?.total"></span>
                        </div>
                        <div class="text-[11px] text-gray-400 text-right" x-text="selectedDrawerOrder?.payment_method + ' · ' + (selectedDrawerOrder?.is_paid ? 'ĐÃ THANH TOÁN' : 'THU TIỀN COD')"></div>
                    </div>

                    <!-- Quick Status Switcher in Drawer (AJAX 1-Chạm) -->
                    <div class="space-y-2 pt-1">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Chuyển trạng thái đơn:</span>
                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedDrawerOrder.id, 'preparing', selectedDrawerOrder.code)"
                                class="p-2 rounded-xl text-xs font-bold transition-colors cursor-pointer"
                                :class="['preparing', 'processing'].includes(selectedDrawerOrder?.status) ? 'bg-orange-600 text-white font-black' : 'bg-orange-50 text-orange-800 hover:bg-orange-100'"
                            >
                                🍳 Đang làm món
                            </button>

                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedDrawerOrder.id, 'delivering', selectedDrawerOrder.code)"
                                class="p-2 rounded-xl text-xs font-bold transition-colors cursor-pointer"
                                :class="['delivering', 'shipping'].includes(selectedDrawerOrder?.status) ? 'bg-blue-600 text-white font-black' : 'bg-blue-50 text-blue-800 hover:bg-blue-100'"
                            >
                                📦 Đang giao hàng
                            </button>

                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedDrawerOrder.id, 'completed', selectedDrawerOrder.code)"
                                class="p-2 rounded-xl text-xs font-bold transition-colors cursor-pointer"
                                :class="selectedDrawerOrder?.status === 'completed' ? 'bg-emerald-600 text-white font-black' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100'"
                            >
                                ✅ Giao xong
                            </button>

                            <button 
                                type="button" 
                                @click="if(confirm('Bạn có chắc muốn huỷ đơn #' + selectedDrawerOrder.code + '?')) updateOrderStatus(selectedDrawerOrder.id, 'cancelled', selectedDrawerOrder.code)"
                                class="p-2 rounded-xl text-xs font-bold transition-colors cursor-pointer"
                                :class="selectedDrawerOrder?.status === 'cancelled' ? 'bg-rose-600 text-white font-black' : 'bg-rose-50 text-rose-800 hover:bg-rose-100'"
                            >
                                ❌ Huỷ đơn
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- 4. MODAL XEM & IN PHIẾU BẾP / HOÁ ĐƠN MINI K80 (80MM) -->
    <div 
        x-show="activePrintOrder" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            
            <div 
                x-show="activePrintOrder"
                class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
                @click="activePrintOrder = null"
            ></div>

            <div 
                x-show="activePrintOrder"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-sm sm:w-full relative z-50 p-5 space-y-4"
            >
                
                <!-- Phiếu in mẫu K80 -->
                <div id="printable-receipt" class="bg-white p-4 border border-gray-200 rounded-2xl text-gray-900 font-mono text-xs space-y-3">
                    
                    <div class="text-center border-b border-dashed border-gray-400 pb-3 space-y-0.5">
                        <h4 class="font-black text-sm uppercase">QUÁN GÀ SỐT GAO</h4>
                        <p class="text-[10px] text-gray-600">{{ $settings['store_address'] ?? 'Hà Nội' }}</p>
                        <p class="text-[10px] text-gray-600">Hotline: {{ $settings['hotline'] ?? '0988.868.GAO' }}</p>
                        <div class="pt-1 font-bold text-xs" x-text="'ĐƠN: #' + activePrintOrder?.code"></div>
                        <div class="text-[10px] text-gray-500" x-text="activePrintOrder?.time"></div>
                    </div>

                    <!-- Khách hàng -->
                    <div class="border-b border-dashed border-gray-400 pb-2 space-y-0.5 text-[11px]">
                        <div><strong>Khách:</strong> <span x-text="activePrintOrder?.name"></span></div>
                        <div><strong>SĐT:</strong> <span x-text="activePrintOrder?.phone"></span></div>
                        <div><strong>Đ/C:</strong> <span x-text="activePrintOrder?.address + ', ' + activePrintOrder?.district"></span></div>
                        <template x-if="activePrintOrder?.driver_note">
                            <div class="pt-0.5 font-bold italic" x-text="'Ghi chú: ' + activePrintOrder?.driver_note"></div>
                        </template>
                    </div>

                    <!-- Danh sách món -->
                    <div class="border-b border-dashed border-gray-400 pb-2 space-y-1 text-[11px]">
                        <template x-for="(it, i) in activePrintOrder?.items" :key="i">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-bold" x-text="it.qty + 'x ' + it.name"></div>
                                    <div class="text-[9px] text-gray-500" x-show="it.sauce || (it.toppings && it.toppings.length > 0)">
                                        <span x-show="it.sauce" x-text="'Sốt ' + it.sauce"></span>
                                        <span x-show="it.toppings && it.toppings.length > 0" x-text="' | ' + (Array.isArray(it.toppings) ? it.toppings.join(', ') : it.toppings)"></span>
                                    </div>
                                </div>
                                <div class="font-bold" x-text="it.price"></div>
                            </div>
                        </template>
                    </div>

                    <!-- Tổng cộng -->
                    <div class="space-y-1 text-[11px]">
                        <div class="flex justify-between">
                            <span>Tạm tính:</span>
                            <span x-text="activePrintOrder?.subtotal"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí ship:</span>
                            <span x-text="activePrintOrder?.shipping"></span>
                        </div>
                        <template x-if="activePrintOrder?.discount">
                            <div class="flex justify-between text-red-600">
                                <span>Giảm giá:</span>
                                <span x-text="activePrintOrder?.discount"></span>
                            </div>
                        </template>
                        <div class="flex justify-between font-black text-sm pt-1 border-t border-gray-400">
                            <span>THU KHÁCH:</span>
                            <span x-text="activePrintOrder?.total"></span>
                        </div>
                        <div class="text-center pt-1 font-bold text-[10px]" x-text="activePrintOrder?.payment_method + ' (' + (activePrintOrder?.is_paid ? 'ĐÃ THANH TOÁN' : 'THU COD') + ')'"></div>
                    </div>

                    <div class="text-center pt-2 text-[9px] text-gray-500 border-t border-dashed border-gray-400">
                        Cảm ơn bạn đã ủng hộ Quán Gà Sốt GAO! Chúc bạn ngon miệng ❤️
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 pt-2">
                    <button 
                        type="button" 
                        onclick="window.print()" 
                        class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                    >
                        🖨️ Bấm In Ngay (Print)
                    </button>
                    <button 
                        type="button" 
                        @click="activePrintOrder = null" 
                        class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs cursor-pointer"
                    >
                        Đóng
                    </button>
                </div>

            </div>

        </div>
    </div>

</div>

<style>
@media print {
    /* Ẩn toàn bộ giao diện web khi in */
    body * {
        visibility: hidden !important;
    }
    
    /* Chỉ hiển thị duy nhất khung phiếu in K80 */
    #printable-receipt, #printable-receipt * {
        visibility: visible !important;
    }
    
    #printable-receipt {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 78mm !important;
        max-width: 78mm !important;
        margin: 0 !important;
        padding: 4mm !important;
        border: none !important;
        box-shadow: none !important;
        background: #fff !important;
        color: #000 !important;
        font-family: monospace, sans-serif !important;
        font-size: 12px !important;
        line-height: 1.3 !important;
    }

    @page {
        size: 80mm auto;
        margin: 0;
    }
}
</style>
@endsection
