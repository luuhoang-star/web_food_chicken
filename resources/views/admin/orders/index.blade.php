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
        newOrdersCount: 0,
        orderStatuses: {},
        cancelDialogOrder: null,
        cancelReasonText: 'Khách yêu cầu huỷ',

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

        openCancelDialog(order) {
            this.cancelDialogOrder = order;
            this.cancelReasonText = 'Khách yêu cầu huỷ';
        },

        confirmCancelOrder() {
            if (!this.cancelDialogOrder) return;
            const order = this.cancelDialogOrder;
            const reason = this.cancelReasonText.trim() || 'Khách yêu cầu huỷ';
            this.cancelDialogOrder = null;
            this.updateOrderStatus(order.id, 'cancelled', order.code, reason);
        },

        handleDropdownStatusChange(orderId, event, orderCode, currentStatus) {
            const newStatus = event.target.value;
            const oldStatus = this.orderStatuses[orderId]?.status || currentStatus;
            
            if (newStatus === oldStatus) return;

            if (newStatus === 'completed') {
                if (!confirm(`Bạn có chắc chắn muốn chuyển đơn #${orderCode} sang trạng thái: ĐÃ GIAO THÀNH CÔNG?`)) {
                    event.target.value = oldStatus;
                    return;
                }
            } else if (newStatus === 'cancelled') {
                event.target.value = oldStatus;
                this.openCancelDialog({ id: orderId, code: orderCode });
                return;
            }

            this.updateOrderStatus(orderId, newStatus, orderCode);
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
            const itemsTxt = order.items.map(i => {
                let sauceStr = '';
                if (i.sauce) {
                    const cleanS = i.sauce.replace(/^sốt\s+/i, '');
                    sauceStr = ` (Sốt ${cleanS})`;
                }
                return `${i.qty}x ${i.name}${sauceStr}`;
            }).join('; ');

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

        async updateOrderStatus(orderId, newStatus, orderCode, cancellationReason = null) {
            try {
                const bodyPayload = { order_status: newStatus };
                if (cancellationReason) {
                    bodyPayload.cancellation_reason = cancellationReason;
                }

                const res = await fetch(`/admin/orders/${orderId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(bodyPayload)
                });
                const data = await res.json();
                if (data.success) {
                    this.orderStatuses[orderId] = {
                        status: data.order_status,
                        label: data.status_label,
                        color: data.status_color,
                        is_paid: data.is_paid,
                        cancellation_reason: data.cancellation_reason
                    };

                    if (this.selectedDrawerOrder && this.selectedDrawerOrder.id === orderId) {
                        this.selectedDrawerOrder.status = data.order_status;
                        this.selectedDrawerOrder.status_label = data.status_label;
                        this.selectedDrawerOrder.status_color = data.status_color;
                        this.selectedDrawerOrder.is_paid = data.is_paid;
                        this.selectedDrawerOrder.cancellation_reason = data.cancellation_reason;
                    }

                    this.showToast(`Đã chuyển đơn #${orderCode || data.order_code} sang: ${data.status_label}!`);
                } else {
                    alert('Không thể cập nhật trạng thái đơn.');
                }
            } catch (e) {
                alert('Lỗi kết nối khi cập nhật đơn hàng.');
            }
        }
    }"
    @keydown.window.escape="selectedDrawerOrder = null; activePrintOrder = null"
    @keydown.window.p="if (selectedDrawerOrder && !activePrintOrder) { openPrintModal(selectedDrawerOrder); }"
    @keydown.window.c="if (selectedDrawerOrder && !activePrintOrder) { copyShipperInfo(selectedDrawerOrder); }"
    @new-order-received.window="newOrdersCount += ($event.detail?.new_count || 1)"
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

    <!-- 2. BẢNG DANH SÁCH ĐƠN HÀNG (RESPONSIVE TABLE, KHÔNG BỊ TRÀN HAY VỠ LAYOUT) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-xs border-collapse">
                
                <thead class="bg-gray-50/90 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-3.5 py-3 w-[135px] whitespace-nowrap">Mã Đơn / Giờ</th>
                        <th class="px-3 py-3 w-[180px] whitespace-nowrap">Khách Hàng</th>
                        <th class="px-3 py-3 min-w-[260px]">Món Bếp Làm</th>
                        <th class="px-2 py-3 w-[85px] text-center whitespace-nowrap">Thanh Toán</th>
                        <th class="px-3 py-3 w-[110px] text-right whitespace-nowrap">Tổng Thu</th>
                        <th class="px-3 py-3 w-[185px] text-center whitespace-nowrap">Trạng Thái</th>
                        <th class="px-3.5 py-3 w-[120px] text-right whitespace-nowrap">Thao Tác</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    @forelse($orders as $order)
                        @php
                            $formattedTotal = number_format((float) $order->total_amount, 0, ',', '.') . ' ₫';
                            $isPaid = ($order->payment_status === 'paid');
                            
                            // Gom nhóm các món trùng thuộc tính để hiển thị phẳng, siêu gọn
                            $groupedItems = [];
                            foreach ($order->items as $item) {
                                $sauce = trim((string) ($item->sauce ?? ''));
                                if ($sauce !== '') {
                                    $cleanSauce = preg_replace('/^sốt\s+/iu', '', $sauce);
                                    $sauce = 'Sốt ' . $cleanSauce;
                                }
                                $toppings = $item->formatted_toppings;
                                $key = md5(($item->product_name ?? '') . '__' . $sauce . '__' . $toppings);
                                
                                if (!isset($groupedItems[$key])) {
                                    $groupedItems[$key] = [
                                        'name' => $item->product_name,
                                        'quantity' => (int) $item->quantity,
                                        'sauce' => $sauce,
                                        'toppings' => $toppings,
                                        'price' => (float) ($item->total_item_price ?: ($item->price * $item->quantity)),
                                    ];
                                } else {
                                    $groupedItems[$key]['quantity'] += (int) $item->quantity;
                                    $groupedItems[$key]['price'] += (float) ($item->total_item_price ?: ($item->price * $item->quantity));
                                }
                            }
                            $groupedItems = array_values($groupedItems);
                            $totalPortions = array_sum(array_column($groupedItems, 'quantity'));
                            $distinctCount = count($groupedItems);

                            // Tính thời gian chờ (Elapsed time)
                            $minutesAgo = $order->created_at ? (int) abs(now()->diffInMinutes($order->created_at)) : 0;
                            $waitText = $minutesAgo < 1 ? 'Vừa xong' : ($minutesAgo < 60 ? $minutesAgo . 'p trước' : (int) ($minutesAgo / 60) . 'h trước');
                            $isPending = ($order->order_status === 'pending');
                            $isOverdue = ($isPending && $minutesAgo >= 10);

                            // JSON object payload for Detail Drawer & Print Modal
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
                                'items' => array_map(fn($gi) => [
                                    'name' => $gi['name'],
                                    'qty' => $gi['quantity'],
                                    'sauce' => $gi['sauce'],
                                    'toppings' => $gi['toppings'],
                                    'price' => number_format($gi['price'], 0, ',', '.') . ' ₫'
                                ], $groupedItems)
                            ];
                        @endphp

                        <tr 
                            class="hover:bg-amber-50/40 transition-colors cursor-pointer {{ $isPending ? 'bg-amber-50/20' : '' }}"
                            :class="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'pending' ? 'bg-amber-50/30' : ''"
                            @click="openDrawer({{ json_encode($orderPayload) }})"
                        >
                            
                            <!-- 1. Mã đơn & Thời gian chờ -->
                            <td class="px-3.5 py-3 align-top whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-black text-gray-900 font-mono text-xs hover:text-red-600 transition-colors">
                                            #{{ $order->order_code }}
                                        </span>
                                        @if($isPending)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-black bg-red-600 text-white animate-pulse shrink-0">Mới</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 text-[11px] text-gray-400">
                                        <span>{{ $order->created_at ? $order->created_at->format('H:i') : '' }}</span>
                                        <span>·</span>
                                        <span class="font-bold {{ $isOverdue ? 'text-red-600 bg-red-50 px-1 rounded' : 'text-gray-500' }}" title="Thời gian từ lúc khách đặt đơn">
                                            ⏱️ {{ $waitText }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Khách hàng & Giao hàng -->
                            <td class="px-3 py-3 align-top">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <strong class="text-gray-900 font-bold text-xs" title="{{ $order->customer_name }}">{{ $order->customer_name }}</strong>
                                        @if($order->district)
                                            <span class="text-gray-400 text-[10px]">·</span>
                                            <span class="text-gray-500 text-[11px] font-semibold">{{ $order->district }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5 text-[11px]" @click.stop>
                                        <a href="tel:{{ $order->customer_phone }}" class="text-emerald-700 font-bold font-mono hover:underline flex items-center gap-1">
                                            <span>📞</span>
                                            <span>{{ $order->customer_phone }}</span>
                                        </a>
                                        <a 
                                            href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->address . ', ' . $order->district . ', Hà Nội') }}" 
                                            target="_blank" 
                                            class="inline-flex items-center hover:opacity-80 transition-opacity p-0.5"
                                            title="Mở Google Maps chỉ đường"
                                        >
                                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 92.3 132.3">
                                                <path fill="#1a73e8" d="M60.2 2.2C55.8.8 51 0 46.1 0 32 0 19.3 6.4 10.8 16.5l21.8 18.3L60.2 2.2z"/>
                                                <path fill="#ea4335" d="M10.8 16.5C4.1 24.5 0 34.9 0 46.1c0 8.7 1.7 15.7 4.6 22l28-33.3-21.8-18.3z"/>
                                                <path fill="#4285f4" d="M46.2 28.5c9.8 0 17.7 7.9 17.7 17.7 0 4.3-1.6 8.3-4.2 11.4 0 0 13.9-16.6 28-33.3C80.8 13.6 69.4 4.8 56.1 1.2L32.6 34.8c3.3-3.9 8.1-6.3 13.6-6.3z"/>
                                                <path fill="#fbbc04" d="M46.2 63.8c-9.8 0-17.7-7.9-17.7-17.7 0-4.3 1.5-8.3 4.1-11.3l-28 33.3c4.8 10.6 12.8 19.2 21 29.9l34.1-40.5c-3.3 3.9-8.1 6.3-13.5 6.3z"/>
                                                <path fill="#34a853" d="M59.6 98c15.2-23.7 32.7-33.8 32.7-51.9 0-7.8-1.9-15.1-5.1-21.6l-48.6 57.8c2.6 3.4 5.3 7.1 8 11.2 7.2 11 5.2 17.7 11.5 17.7 6.3 0 4.3-6.7 11.5-17.7z"/>
                                            </svg>
                                        </a>
                                    </div>

                                    @if(!empty($order->driver_note))
                                        <div class="text-[10px] text-amber-900 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/70 font-medium inline-block max-w-full truncate" title="{{ $order->driver_note }}">
                                            📝 {{ $order->driver_note }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- 3. Món bếp làm -->
                            <td class="px-3 py-3 align-top">
                                <div class="space-y-1.5">
                                    <!-- Header tóm tắt tổng số suất -->
                                    <div class="flex items-center gap-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-orange-50 text-orange-950 border border-orange-200/80 text-[10px] font-black shrink-0">
                                            🍗 {{ $totalPortions }} suất @if($distinctCount > 1) <span class="text-orange-700 font-normal">({{ $distinctCount }} món)</span> @endif
                                        </span>
                                    </div>

                                    <!-- Danh sách các món với badge số lượng gắn liền -->
                                    <div class="space-y-1">
                                        @foreach($groupedItems as $item)
                                            @php
                                                $itemDetails = [];
                                                if (!empty($item['sauce'])) {
                                                    $itemDetails[] = $item['sauce'];
                                                }
                                                if (!empty($item['toppings'])) {
                                                    $itemDetails[] = is_array($item['toppings']) ? implode(', ', $item['toppings']) : $item['toppings'];
                                                }
                                            @endphp
                                            <div class="flex items-start gap-1.5 text-xs leading-snug">
                                                <span class="font-black font-mono text-[10px] shrink-0 px-1.5 py-0.2 bg-red-50 text-red-700 rounded border border-red-200">
                                                    ×{{ $item['quantity'] }}
                                                </span>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-gray-900" title="{{ $item['name'] }}">{{ $item['name'] }}</span>
                                                    @if(!empty($itemDetails))
                                                        <span class="text-[11px] text-gray-500 font-normal">
                                                            ({{ implode(', ', $itemDetails) }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>

                            <!-- 4. Thanh toán -->
                            <td class="px-2 py-3 align-top whitespace-nowrap text-center">
                                <span 
                                    class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black border"
                                    :class="(orderStatuses[{{ $order->id }}]?.is_paid ?? {{ $isPaid ? 'true' : 'false' }}) ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-900 border-amber-200'"
                                    x-text="(orderStatuses[{{ $order->id }}]?.is_paid ?? {{ $isPaid ? 'true' : 'false' }}) ? '💳 CK' : '💵 COD'"
                                >
                                    {{ $isPaid ? '💳 CK' : '💵 COD' }}
                                </span>
                            </td>

                            <!-- 5. Tổng thu -->
                            <td class="px-3 py-3 align-top whitespace-nowrap text-right">
                                <div class="space-y-0.5">
                                    <span class="font-black text-red-600 text-xs sm:text-sm block tracking-tight">
                                        {{ $formattedTotal }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium block">
                                        {{ (float) $order->shipping_fee === 0.0 ? 'Freeship' : '+' . number_format((float) $order->shipping_fee, 0, ',', '.') . '₫' }}
                                    </span>
                                </div>
                            </td>

                            <!-- 6. Trạng thái (Dropdown đổi nhanh kèm Confirm Dialog, Khóa khi đã Hoàn thành / Đã huỷ) -->
                            <td class="px-3 py-3 align-middle whitespace-nowrap text-center" @click.stop>
                                <template x-if="['completed', 'cancelled'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}')">
                                    <div class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black border shadow-2xs"
                                        :class="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'completed' ? 'bg-emerald-50 text-emerald-900 border-emerald-300' : 'bg-rose-50 text-rose-900 border-rose-300'"
                                        :title="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'completed' ? 'Đơn hàng đã hoàn tất (Đã chốt, không đổi trạng thái)' : 'Đơn hàng đã huỷ (Đã chốt, không đổi trạng thái)'"
                                    >
                                        <span class="text-[11px]">🔒</span>
                                        <span x-text="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'completed' ? '✅ Đã giao xong' : '❌ Đã hủy đơn'"></span>
                                    </div>
                                </template>

                                <template x-if="!['completed', 'cancelled'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}')">
                                    <div class="relative inline-block w-full max-w-[170px] text-left group">
                                        <select 
                                            :value="orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}'"
                                            @change="handleDropdownStatusChange({{ $order->id }}, $event, '{{ $order->order_code }}', '{{ $order->order_status }}')"
                                            class="w-full pl-2.5 pr-6 py-1.5 rounded-xl text-xs font-black border shadow-2xs appearance-none cursor-pointer focus:outline-none focus:ring-2 transition-all font-sans"
                                            :class="{
                                                'bg-amber-50 text-amber-900 border-amber-300 hover:bg-amber-100 focus:ring-amber-400': (orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'pending',
                                                'bg-orange-50 text-orange-950 border-orange-300 hover:bg-orange-100 focus:ring-orange-400': ['confirmed', 'preparing', 'processing'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}'),
                                                'bg-blue-50 text-blue-950 border-blue-300 hover:bg-blue-100 focus:ring-blue-400': ['delivering', 'shipping'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}'),
                                            }"
                                            title="Bấm vào để đổi trạng thái đơn hàng (Có xác nhận với trạng thái cuối)"
                                        >
                                            <option value="pending" class="bg-white text-amber-900 font-bold py-1">🕒 Chờ xử lý (Mới)</option>
                                            <option value="preparing" class="bg-white text-orange-900 font-bold py-1">🍳 Đang làm món</option>
                                            <option value="delivering" class="bg-white text-blue-900 font-bold py-1">📦 Đang giao hàng</option>
                                            <option value="completed" class="bg-white text-emerald-900 font-bold py-1">✅ Đã giao thành công</option>
                                            <option value="cancelled" class="bg-white text-rose-900 font-bold py-1">❌ Đã hủy đơn</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-current opacity-70 group-hover:opacity-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>
                                </template>
                            </td>

                            <!-- 7. Thao tác -->
                            <td class="px-3.5 py-3 align-middle whitespace-nowrap text-right" @click.stop>
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <button 
                                        type="button" 
                                        @click="openPrintModal({{ json_encode($orderPayload) }})"
                                        class="p-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors cursor-pointer shadow-2xs text-xs flex items-center justify-center shrink-0"
                                        title="In phiếu bếp K80 (Phím P)"
                                    >
                                        🖨️
                                    </button>

                                    <button 
                                        type="button" 
                                        @click="openDrawer({{ json_encode($orderPayload) }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white font-bold transition-colors cursor-pointer shadow-2xs text-xs flex items-center gap-1 shrink-0"
                                        title="Mở chi tiết đơn hàng (Phím C: Copy, Esc: Đóng)"
                                    >
                                        <span>Chi tiết</span>
                                        <span>↗</span>
                                    </button>
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
                    
                    <!-- 1-Touch Action Suite Toolbar (Nút Gọi Khách To Bản + Các Icon Buttons Tiện Lợi) -->
                    <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200 space-y-2.5">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Thao tác 1-chạm (Phím C: Copy, P: In):</span>
                        
                        <!-- Nút Gọi Khách To Bản (Nút Chính, Ưu Tiên Số 1) -->
                        <a 
                            :href="'tel:' + selectedDrawerOrder?.phone" 
                            class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs transition-all flex items-center justify-center gap-2"
                        >
                            <span class="text-sm">📞</span>
                            <span>Gọi khách ngay</span>
                            <span class="font-mono text-[11px] opacity-90" x-text="'(' + selectedDrawerOrder?.phone + ')'"></span>
                        </a>

                        <!-- Các Icon Buttons Đặt Cạnh Nhau -->
                        <div class="grid grid-cols-4 gap-2 pt-1">
                            <a 
                                :href="'https://zalo.me/' + (selectedDrawerOrder?.phone || '').replace(/[^0-9]/g, '')" 
                                target="_blank" 
                                class="py-2 px-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 font-bold text-center border border-sky-200 transition-colors flex flex-col items-center justify-center gap-0.5 text-[11px]"
                                title="Nhắn Zalo"
                            >
                                <span class="text-sm">💬</span>
                                <span>Zalo</span>
                            </a>

                            <a 
                                :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent((selectedDrawerOrder?.address || '') + ', ' + (selectedDrawerOrder?.district || '') + ', Hà Nội')" 
                                target="_blank" 
                                class="py-2 px-2 rounded-xl bg-white hover:bg-red-50 text-slate-800 font-bold text-center border border-slate-200 hover:border-red-300 transition-colors flex flex-col items-center justify-center gap-1 text-[11px] shadow-2xs group"
                                title="Mở Google Maps chỉ đường"
                            >
                                <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 92.3 132.3">
                                    <path fill="#1a73e8" d="M60.2 2.2C55.8.8 51 0 46.1 0 32 0 19.3 6.4 10.8 16.5l21.8 18.3L60.2 2.2z"/>
                                    <path fill="#ea4335" d="M10.8 16.5C4.1 24.5 0 34.9 0 46.1c0 8.7 1.7 15.7 4.6 22l28-33.3-21.8-18.3z"/>
                                    <path fill="#4285f4" d="M46.2 28.5c9.8 0 17.7 7.9 17.7 17.7 0 4.3-1.6 8.3-4.2 11.4 0 0 13.9-16.6 28-33.3C80.8 13.6 69.4 4.8 56.1 1.2L32.6 34.8c3.3-3.9 8.1-6.3 13.6-6.3z"/>
                                    <path fill="#fbbc04" d="M46.2 63.8c-9.8 0-17.7-7.9-17.7-17.7 0-4.3 1.5-8.3 4.1-11.3l-28 33.3c4.8 10.6 12.8 19.2 21 29.9l34.1-40.5c-3.3 3.9-8.1 6.3-13.5 6.3z"/>
                                    <path fill="#34a853" d="M59.6 98c15.2-23.7 32.7-33.8 32.7-51.9 0-7.8-1.9-15.1-5.1-21.6l-48.6 57.8c2.6 3.4 5.3 7.1 8 11.2 7.2 11 5.2 17.7 11.5 17.7 6.3 0 4.3-6.7 11.5-17.7z"/>
                                </svg>
                                <span>Maps</span>
                            </a>

                            <button 
                                type="button" 
                                @click="copyShipperInfo(selectedDrawerOrder)"
                                class="py-2 px-2 rounded-xl bg-white hover:bg-purple-50 text-slate-800 font-bold text-center border border-slate-200 hover:border-purple-300 transition-colors flex flex-col items-center justify-center gap-1 text-[11px] shadow-2xs cursor-pointer group"
                                title="Sao chép gửi shipper (Phím C)"
                            >
                                <svg class="w-5 h-5 text-slate-600 group-hover:text-purple-700 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                                <span>Copy</span>
                            </button>

                            <button 
                                type="button" 
                                @click="openPrintModal(selectedDrawerOrder)"
                                class="py-2 px-2 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-center transition-colors flex flex-col items-center justify-center gap-0.5 text-[11px] cursor-pointer"
                                title="In phiếu K80"
                            >
                                <span class="text-sm">🖨️</span>
                                <span>In K80</span>
                            </button>
                        </div>
                    </div>

                    <!-- Customer Delivery Info (Tách bạch rõ ràng từng mục) -->
                    <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200 space-y-2.5">
                        
                        <!-- 2 Cột: Tên khách & SĐT -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <!-- Khách hàng -->
                            <div class="bg-white p-2.5 rounded-xl border border-gray-200/90 shadow-2xs space-y-0.5">
                                <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider flex items-center gap-1">
                                    <span>👤</span>
                                    <span>Khách hàng</span>
                                </span>
                                <span class="font-black text-xs sm:text-sm text-gray-900 block truncate" x-text="selectedDrawerOrder?.name"></span>
                            </div>

                            <!-- SĐT -->
                            <div class="bg-white p-2.5 rounded-xl border border-gray-200/90 shadow-2xs space-y-0.5">
                                <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider flex items-center gap-1">
                                    <span>📞</span>
                                    <span>Số điện thoại</span>
                                </span>
                                <a 
                                    :href="'tel:' + selectedDrawerOrder?.phone" 
                                    class="font-mono font-black text-xs sm:text-sm text-red-600 hover:text-red-700 hover:underline block truncate flex items-center gap-1"
                                    title="Bấm gọi ngay"
                                >
                                    <span x-text="selectedDrawerOrder?.phone"></span>
                                    <span class="text-[10px] font-sans text-red-500 font-semibold">(Gọi ↗)</span>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Địa chỉ giao nhận -->
                        <div class="bg-white p-2.5 rounded-xl border border-gray-200/90 shadow-2xs space-y-1">
                            <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider flex items-center gap-1">
                                <span>📍</span>
                                <span>Địa chỉ giao nhận</span>
                            </span>
                            <div class="text-xs text-gray-900 leading-relaxed font-bold">
                                <span x-text="selectedDrawerOrder?.address"></span><template x-if="selectedDrawerOrder?.district"><span class="text-gray-600 font-semibold" x-text="', ' + selectedDrawerOrder?.district"></span></template>
                            </div>
                        </div>

                        <!-- Ghi chú tài xế -->
                        <template x-if="selectedDrawerOrder?.driver_note && selectedDrawerOrder.driver_note.trim() !== ''">
                            <div class="p-2.5 bg-amber-50 rounded-xl text-amber-950 text-xs font-medium border border-amber-200/90 flex items-start gap-1.5">
                                <span class="shrink-0 text-sm">📝</span>
                                <div class="space-y-0.5">
                                    <span class="text-[10px] font-black text-amber-800 uppercase tracking-wide block">Ghi chú từ khách:</span>
                                    <p class="font-bold italic" x-text="'“' + selectedDrawerOrder.driver_note + '”'"></p>
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
                                        <span class="font-bold text-gray-700 text-xs" x-text="it.price"></span>
                                    </div>
                                    <div class="text-[10px] text-gray-500" x-show="(it.sauce && !it.name.toLowerCase().includes(it.sauce.toLowerCase().replace(/^sốt\s+/, ''))) || (it.toppings && it.toppings.length > 0)">
                                        <span x-show="it.sauce && !it.name.toLowerCase().includes(it.sauce.toLowerCase().replace(/^sốt\s+/, ''))" x-text="it.sauce.toLowerCase().startsWith('sốt') ? it.sauce : 'Sốt ' + it.sauce"></span>
                                        <span x-show="(it.sauce && !it.name.toLowerCase().includes(it.sauce.toLowerCase().replace(/^sốt\s+/, ''))) && it.toppings && it.toppings.length > 0"> · </span>
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
                    </div>

                    <!-- CẢNH BÁO / LÝ DO HUỶ ĐƠN NẾU CÓ -->
                    <template x-if="selectedDrawerOrder?.status === 'cancelled' && selectedDrawerOrder?.cancellation_reason">
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-xs space-y-1">
                            <span class="text-[10px] font-black uppercase text-rose-800 tracking-wider flex items-center gap-1">
                                <span>❌</span>
                                <span>Lý do huỷ đơn:</span>
                            </span>
                            <p class="font-bold text-rose-900" x-text="selectedDrawerOrder.cancellation_reason"></p>
                        </div>
                    </template>

                </div>

                <!-- Drawer Footer (CTA 1-Chạm & Huỷ Đơn) -->
                <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <div class="flex-1">
                        <template x-if="selectedDrawerOrder?.status === 'pending'">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedDrawerOrder.id, 'preparing', selectedDrawerOrder.code)"
                                class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <span>🍳 Nhận & Làm món</span>
                            </button>
                        </template>

                        <template x-if="selectedDrawerOrder?.status === 'confirmed'">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedDrawerOrder.id, 'preparing', selectedDrawerOrder.code)"
                                class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <span>🔥 Bắt đầu làm món</span>
                            </button>
                        </template>

                        <template x-if="['preparing', 'processing'].includes(selectedDrawerOrder?.status)">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedDrawerOrder.id, 'delivering', selectedDrawerOrder.code)"
                                class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <span>📦 Đóng gói & Đi giao</span>
                            </button>
                        </template>

                        <template x-if="['delivering', 'shipping'].includes(selectedDrawerOrder?.status)">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedDrawerOrder.id, 'completed', selectedDrawerOrder.code)"
                                class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <span>✅ Giao thành công</span>
                            </button>
                        </template>

                        <template x-if="selectedDrawerOrder?.status === 'completed'">
                            <div class="w-full py-2.5 rounded-xl bg-emerald-50 text-emerald-800 font-black text-xs text-center border border-emerald-200">
                                ✓ Đơn đã hoàn tất
                            </div>
                        </template>

                        <template x-if="selectedDrawerOrder?.status === 'cancelled'">
                            <div class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs text-center">
                                ✕ Đơn đã huỷ
                            </div>
                        </template>
                    </div>

                    <!-- Nút Huỷ đơn -->
                    <template x-if="selectedDrawerOrder && !['completed', 'cancelled'].includes(selectedDrawerOrder?.status)">
                        <button 
                            type="button" 
                            @click="openCancelDialog(selectedDrawerOrder)" 
                            class="py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-colors cursor-pointer"
                            title="Huỷ đơn hàng này"
                        >
                            ✕ Huỷ
                        </button>
                    </template>

                    <button 
                        type="button" 
                        @click="selectedDrawerOrder = null" 
                        class="py-2.5 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors cursor-pointer"
                    >
                        Đóng
                    </button>
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
                                        <span x-show="it.sauce" x-text="it.sauce.toLowerCase().startsWith('sốt') ? it.sauce : 'Sốt ' + it.sauce"></span>
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

    <!-- 5. DIALOG CHỌN LÝ DO HUỶ ĐƠN HÀNG -->
    <div 
        x-show="cancelDialogOrder" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div 
                x-show="cancelDialogOrder"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/70 backdrop-blur-xs" 
                @click="cancelDialogOrder = null"
            ></div>

            <div 
                x-show="cancelDialogOrder"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-sm relative p-5 space-y-4 text-xs z-50 border border-slate-200"
            >
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">⚠️</span>
                        <h4 class="font-black text-sm text-slate-900">Xác Nhận Huỷ Đơn</h4>
                    </div>
                    <button @click="cancelDialogOrder = null" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <p class="text-slate-600 text-xs">
                    Bạn đang thực hiện huỷ đơn <strong class="font-mono text-slate-900" x-text="'#' + cancelDialogOrder?.code"></strong>. Vui lòng chọn hoặc nhập lý do huỷ:
                </p>

                <!-- Các lý do gợi ý nhanh -->
                <div class="space-y-1.5">
                    <button type="button" @click="cancelReasonText = 'Khách yêu cầu huỷ'" class="w-full text-left px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors" :class="cancelReasonText === 'Khách yêu cầu huỷ' ? 'bg-rose-50 border-rose-300 text-rose-800' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                        • Khách yêu cầu huỷ
                    </button>
                    <button type="button" @click="cancelReasonText = 'Quán hết món / hết sốt'" class="w-full text-left px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors" :class="cancelReasonText === 'Quán hết món / hết sốt' ? 'bg-rose-50 border-rose-300 text-rose-800' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                        • Quán hết món / hết sốt
                    </button>
                    <button type="button" @click="cancelReasonText = 'Không liên lạc được khách hàng'" class="w-full text-left px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors" :class="cancelReasonText === 'Không liên lạc được khách hàng' ? 'bg-rose-50 border-rose-300 text-rose-800' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                        • Không liên lạc được khách hàng
                    </button>
                    <button type="button" @click="cancelReasonText = 'Địa chỉ ngoài vùng giao hàng'" class="w-full text-left px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors" :class="cancelReasonText === 'Địa chỉ ngoài vùng giao hàng' ? 'bg-rose-50 border-rose-300 text-rose-800' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                        • Địa chỉ ngoài vùng giao hàng
                    </button>
                </div>

                <!-- Input nhập lý do tự do -->
                <div>
                    <input 
                        type="text" 
                        x-model="cancelReasonText" 
                        placeholder="Hoặc nhập lý do khác..." 
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium outline-none focus:border-red-500 focus:bg-white"
                    >
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="confirmCancelOrder()" 
                        class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs transition-colors cursor-pointer shadow-xs"
                    >
                        Xác Nhận Huỷ
                    </button>
                    <button 
                        type="button" 
                        @click="cancelDialogOrder = null" 
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer"
                    >
                        Bỏ qua
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
