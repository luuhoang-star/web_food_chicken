@extends('layouts.admin')

@section('title', 'Tổng Quan Bảng Điều Khiển')
@section('page_title', '📊 Tổng Quan')

@section('content')
<div 
    class="space-y-6" 
    x-data="{
        chartMode: 'revenue',
        selectedOrder: null,
        showCustomDate: {{ $range === 'custom' ? 'true' : 'false' }},
        copyToast: '',
        orderStatuses: {},
        pendingCount: {{ $pendingOrdersCount }},
        
        chartLabels: {{ json_encode($chartLabels) }},
        chartRevenues: {{ json_encode($chartRevenues) }},
        chartOrders: {{ json_encode($chartOrders) }},
        chartInstance: null,

        initChart() {
            const ctx = document.getElementById('dashboardChart');
            if (!ctx) return;

            if (this.chartInstance) {
                this.chartInstance.destroy();
            }

            const isRevenue = this.chartMode === 'revenue';
            const dataValues = isRevenue ? this.chartRevenues : this.chartOrders;
            const color = isRevenue ? '#dc2626' : '#2563eb';
            const bgColor = isRevenue ? 'rgba(220, 38, 38, 0.08)' : 'rgba(37, 99, 235, 0.08)';

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.chartLabels,
                    datasets: [{
                        label: isRevenue ? 'Doanh thu (₫)' : 'Số đơn hàng',
                        data: dataValues,
                        borderColor: color,
                        backgroundColor: bgColor,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: color
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    return isRevenue 
                                        ? new Intl.NumberFormat('vi-VN').format(context.raw) + ' ₫'
                                        : context.raw + ' đơn';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: {
                                font: { size: 10 },
                                callback: function(value) {
                                    if (isRevenue) {
                                        if (value >= 1000000) return (value / 1000000) + 'M';
                                        if (value >= 1000) return (value / 1000) + 'k';
                                        return value;
                                    }
                                    return Number.isInteger(value) ? value : '';
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        },

        toggleChartMode(mode) {
            this.chartMode = mode;
            this.initChart();
        },

        showToast(msg) {
            this.copyToast = msg;
            setTimeout(() => { this.copyToast = ''; }, 3500);
        },

        openDetailModal(order) {
            this.selectedOrder = order;
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

                    if (this.selectedOrder && this.selectedOrder.id === orderId) {
                        this.selectedOrder.status = data.order_status;
                        this.selectedOrder.status_label = data.status_label;
                        this.selectedOrder.status_color = data.status_color;
                        this.selectedOrder.is_paid = data.is_paid;
                    }

                    if (newStatus === 'completed' || newStatus === 'cancelled') {
                        if (this.pendingCount > 0) this.pendingCount--;
                    }

                    this.showToast(`Đã chuyển đơn #${orderCode || data.order_code} sang: ${data.status_label}!`);
                } else {
                    alert('Không thể cập nhật trạng thái đơn.');
                }
            } catch (e) {
                alert('Lỗi kết nối khi cập nhật đơn hàng.');
            }
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

            const successMsg = `Đã sao chép đơn #${order.code} để gửi Shipper / AhaMove / Grab!`;

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
        }
    }" 
    x-init="$nextTick(() => initChart())"
    @keydown.window.escape="selectedOrder = null"
    @keydown.window.c="if (selectedOrder) copyShipperInfo(selectedOrder)"
>

    <!-- FLOATING TOAST NOTIFICATION -->
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

    <!-- 1. BỘ LỌC THỜI GIAN TINH GỌN (Pill Tabs Hiện Đại) -->
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        
        <!-- Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 text-xs font-bold scrollbar-none">
            <a 
                href="{{ route('admin.dashboard', ['range' => 'today']) }}" 
                class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ $range === 'today' ? 'bg-gray-900 text-white shadow-xs font-black' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                Hôm nay
            </a>
            <a 
                href="{{ route('admin.dashboard', ['range' => '7days']) }}" 
                class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ $range === '7days' ? 'bg-gray-900 text-white shadow-xs font-black' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                7 ngày
            </a>
            <a 
                href="{{ route('admin.dashboard', ['range' => '30days']) }}" 
                class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ $range === '30days' ? 'bg-gray-900 text-white shadow-xs font-black' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                30 ngày
            </a>
            <a 
                href="{{ route('admin.dashboard', ['range' => 'this_month']) }}" 
                class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ $range === 'this_month' ? 'bg-gray-900 text-white shadow-xs font-black' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                Tháng này
            </a>
            <button 
                type="button" 
                @click="showCustomDate = !showCustomDate"
                class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all cursor-pointer {{ $range === 'custom' ? 'bg-gray-900 text-white shadow-xs font-black' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                Tùy chỉnh ▾
            </button>
        </div>

        <!-- Date Range Display Text -->
        <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
            <span class="text-gray-400">📅</span>
            <span class="font-mono bg-gray-100 px-2.5 py-1 rounded-lg text-gray-900">{{ $dateDisplay }}</span>
        </div>

    </div>

    <!-- Custom Date Picker Collapse Box -->
    <div 
        x-show="showCustomDate" 
        x-transition 
        class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs"
        x-cloak
    >
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <input type="hidden" name="range" value="custom">
            <div class="flex items-center gap-1.5 font-bold text-gray-700">
                <span>Từ ngày:</span>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200 font-mono outline-none focus:border-red-500">
            </div>
            <div class="flex items-center gap-1.5 font-bold text-gray-700">
                <span>Đến ngày:</span>
                <input type="date" name="to_date" value="{{ $toDate }}" class="px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200 font-mono outline-none focus:border-red-500">
            </div>
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white font-black transition-colors cursor-pointer">
                Lọc dữ liệu
            </button>
        </form>
    </div>

    <!-- 2. 4 THẺ KPI CHỈ SỐ CAO CẤP (TINH GỌN, CÂN ĐỐI, PHÂN BIỆT RÕ RÀNG) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- CARD 1: DOANH THU THỰC NHẬN -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs hover:border-gray-300 transition-all flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Doanh thu {{ $range === 'today' ? 'hôm nay' : 'kỳ này' }}
                </span>
                <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                    💰
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight block">
                    {{ number_format($periodRevenue, 0, ',', '.') }} <span class="text-lg font-bold text-gray-500">₫</span>
                </span>
            </div>
            <div class="pt-1 border-t border-gray-100 flex items-center justify-between text-[11px]">
                @if($revenueGrowthPercent !== null)
                    <span class="font-bold flex items-center gap-1 {{ $revenueGrowthPercent >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        <span>{{ $revenueGrowthPercent >= 0 ? '↗' : '↘' }}</span>
                        <span>{{ abs($revenueGrowthPercent) }}% so với {{ $compareLabel }}</span>
                    </span>
                @else
                    <span class="text-gray-400 font-medium">Doanh thu thực tế</span>
                @endif
                <span class="text-gray-400 font-mono">{{ $range === 'today' ? 'Realtime' : 'Tổng kết' }}</span>
            </div>
        </div>

        <!-- CARD 2: TỔNG SỐ ĐƠN HÀNG -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs hover:border-gray-300 transition-all flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Tổng đơn {{ $range === 'today' ? 'hôm nay' : 'kỳ này' }}
                </span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                    📦
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight block">
                    {{ $periodOrdersCount }} <span class="text-lg font-bold text-gray-500">đơn</span>
                </span>
            </div>
            <div class="pt-1 border-t border-gray-100 flex items-center justify-between text-[11px]">
                <span class="font-medium text-gray-600">
                    <strong class="text-emerald-600 font-bold">{{ $completedOrdersCount }}</strong> xong · <span class="text-gray-400">{{ $cancelledOrdersCount }} huỷ</span>
                </span>
                <span class="text-gray-400">
                    {{ $todayStats['dishes_count'] ?? 0 }} suất món
                </span>
            </div>
        </div>

        <!-- CARD 3: ĐƠN CẦN XỬ LÝ (ACTIONABLE PRIORITY) -->
        <div 
            class="bg-white p-5 rounded-2xl border-2 shadow-xs transition-all flex flex-col justify-between space-y-3"
            :class="pendingCount > 0 ? 'border-red-500/80 bg-gradient-to-br from-red-50/40 to-white ring-2 ring-red-500/10' : 'border-gray-200/80'"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Đơn cần xử lý ngay
                </span>
                <div class="relative">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                        ⚡
                    </div>
                    <span x-show="pendingCount > 0" class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-red-600 animate-ping"></span>
                </div>
            </div>
            <div>
                <span 
                    class="text-2xl sm:text-3xl font-black tracking-tight block"
                    :class="pendingCount > 0 ? 'text-red-600' : 'text-gray-900'"
                    x-text="pendingCount + ' đơn'"
                >
                    {{ $pendingOrdersCount }} đơn
                </span>
            </div>
            <div class="pt-1 border-t border-gray-100 flex items-center justify-between text-[11px]">
                <template x-if="pendingCount > 0">
                    <a href="#urgent-queue" class="font-black text-red-600 hover:text-red-700 flex items-center gap-1">
                        <span>Xử lý ngay</span>
                        <span>→</span>
                    </a>
                </template>
                <template x-if="pendingCount === 0">
                    <span class="text-emerald-600 font-bold flex items-center gap-1">
                        <span>✓</span>
                        <span>Bếp đã hết đơn chờ</span>
                    </span>
                </template>
                <span class="text-gray-400">Ưu tiên số 1</span>
            </div>
        </div>

        <!-- CARD 4: GIÁ TRỊ ĐƠN TB & TỶ LỆ HOÀN THÀNH -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs hover:border-gray-300 transition-all flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Giá trị TB / Đơn (AOV)
                </span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                    🎯
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight block">
                    {{ number_format($avgOrderValue, 0, ',', '.') }} <span class="text-lg font-bold text-gray-500">₫</span>
                </span>
            </div>
            <div class="pt-1 border-t border-gray-100 flex items-center justify-between text-[11px]">
                <span class="text-gray-500 font-medium">
                    @php
                        $rate = ($periodOrdersCount > 0) ? round(($completedOrdersCount / $periodOrdersCount) * 100) : 100;
                    @endphp
                    <strong class="text-gray-900 font-bold">{{ $rate }}%</strong> giao thành công
                </span>
                <span class="text-gray-400">Hiệu suất</span>
            </div>
        </div>

    </div>

    <!-- 3. KHU VỰC QUAN TRỌNG: 🔴 ĐƠN CẦN XỬ LÝ NGAY (GỌN GÀNG, KHÔNG RỐI MẮT) -->
    <div id="urgent-queue" class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-4 sm:p-5 space-y-4">
        
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div class="flex items-center gap-2.5">
                <h2 class="text-sm sm:text-base font-black text-gray-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600 inline-block animate-pulse"></span>
                    <span>Đơn hàng đang chờ xử lý</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black" :class="pendingCount > 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600'" x-text="pendingCount + ' đơn'">
                    </span>
                </h2>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 transition-colors flex items-center gap-1">
                <span>Xem toàn bộ đơn</span>
                <span>→</span>
            </a>
        </div>

        @if($actionableOrders->isNotEmpty())
            <!-- Grid các đơn cần hành động ngay lập tức -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($actionableOrders as $order)
                    @php
                        $itemsTxt = $order->items->map(fn($i) => $i->product_name . ' × ' . $i->quantity)->implode(', ');
                        $isPaid = ($order->payment_status === 'paid');
                        
                        $modalPayload = [
                            'id' => $order->id,
                            'code' => $order->order_code,
                            'status' => $order->order_status,
                            'status_label' => $order->status_label,
                            'status_color' => $order->status_color,
                            'time' => $order->created_at ? $order->created_at->format('H:i · d/m/Y') : '',
                            'name' => $order->customer_name,
                            'phone' => $order->customer_phone,
                            'address' => $order->address,
                            'district' => $order->district,
                            'driver_note' => $order->driver_note,
                            'payment_method' => $order->payment_method_label,
                            'is_paid' => $isPaid,
                            'shipping_text' => (float) $order->shipping_fee === 0.0 ? 'Freeship' : 'Phí ship ' . number_format((float) $order->shipping_fee, 0, ',', '.') . ' ₫',
                            'total' => number_format((float) $order->total_amount, 0, ',', '.') . ' ₫',
                            'items' => $order->items->map(fn($item) => [
                                'name' => $item->product_name,
                                'qty' => $item->quantity,
                                'sauce' => $item->sauce,
                                'toppings' => $item->toppings,
                                'price' => number_format((float) ($item->total_item_price ?: ($item->price * $item->quantity)), 0, ',', '.') . ' ₫'
                            ])
                        ];
                    @endphp
                    <div 
                        class="p-4 rounded-2xl border border-gray-200/90 bg-gray-50/40 hover:bg-white hover:border-gray-300 transition-all space-y-3 flex flex-col justify-between shadow-2xs"
                        x-show="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') !== 'completed' && (orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') !== 'cancelled'"
                    >
                        
                        <div class="space-y-2">
                            <!-- Top: Mã đơn & Trạng thái -->
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-black text-xs text-gray-900 block">
                                    #{{ $order->order_code }}
                                </span>
                                <span 
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full border" 
                                    :class="orderStatuses[{{ $order->id }}]?.color || '{{ $order->status_color }}'"
                                    x-text="orderStatuses[{{ $order->id }}]?.label || '{{ $order->status_label }}'"
                                >
                                </span>
                            </div>

                            <!-- Khách hàng & SĐT & Địa chỉ -->
                            <div class="text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <strong class="text-gray-900 font-black text-xs uppercase">{{ $order->customer_name }}</strong>
                                    <a href="tel:{{ $order->customer_phone }}" class="text-red-600 font-mono font-black text-[11px] hover:underline" @click.stop>
                                        📞 {{ $order->customer_phone }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-gray-700 bg-white p-2 rounded-xl border border-gray-100 font-medium leading-tight">
                                    <span class="text-red-500">📍</span>
                                    <span class="font-bold text-gray-900">{{ $order->address }}</span>, <strong class="text-gray-800">{{ $order->district }}</strong>
                                </div>
                            </div>

                            <!-- Món ăn -->
                            <p class="text-xs text-gray-800 font-semibold line-clamp-2 leading-snug">
                                🍗 {{ $itemsTxt }}
                            </p>

                            <!-- Tiền thu -->
                            <div class="flex items-center justify-between text-xs pt-1 border-t border-gray-200/60">
                                <span class="text-gray-500 font-medium">Tổng thu:</span>
                                <div class="text-right">
                                    <span class="font-black text-red-600 text-sm">
                                        {{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫
                                    </span>
                                    <span class="text-[10px] block font-bold {{ $isPaid ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $isPaid ? '✓ Đã CK VietQR' : '• Thu tiền COD' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 1 PRIMARY CTA DUY NHẤT DỰA VÀO TRẠNG THÁI (AJAX 1-CHẠM) -->
                        <div class="pt-2 border-t border-dashed border-gray-200 flex items-center gap-2">
                            
                            <template x-if="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'pending'">
                                <button 
                                    type="button" 
                                    @click="updateOrderStatus({{ $order->id }}, 'preparing', '{{ $order->order_code }}')"
                                    class="flex-1 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer"
                                >
                                    ✓ Nhận & Làm Món
                                </button>
                            </template>

                            <template x-if="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'confirmed'">
                                <button 
                                    type="button" 
                                    @click="updateOrderStatus({{ $order->id }}, 'preparing', '{{ $order->order_code }}')"
                                    class="flex-1 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer"
                                >
                                    🔥 Bếp Chiên Gà
                                </button>
                            </template>

                            <template x-if="['preparing', 'processing'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}')">
                                <button 
                                    type="button" 
                                    @click="updateOrderStatus({{ $order->id }}, 'delivering', '{{ $order->order_code }}')"
                                    class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer"
                                >
                                    📦 Đóng Gói & Đi Giao
                                </button>
                            </template>

                            <template x-if="['delivering', 'shipping'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}')">
                                <button 
                                    type="button" 
                                    @click="updateOrderStatus({{ $order->id }}, 'completed', '{{ $order->order_code }}')"
                                    class="flex-1 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer"
                                >
                                    ✅ Đã Giao Xong
                                </button>
                            </template>

                            <!-- Nút xem chi tiết -->
                            <button 
                                type="button" 
                                @click="openDetailModal({{ json_encode($modalPayload) }})"
                                class="px-2.5 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 text-xs font-bold transition-colors cursor-pointer shadow-2xs"
                                title="Xem chi tiết đơn"
                            >
                                👁️
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="py-6 text-center text-xs font-bold text-gray-500 bg-gray-50/60 rounded-2xl border border-dashed border-gray-200">
                🎉 Tuyệt vời! Hiện không có đơn nào đang chờ xử lý. Bếp đã hoàn thành tất cả đơn hàng.
            </div>
        @endif

    </div>

    <!-- 4. 2-COLUMN LAYOUT: BIỂU ĐỒ DOANH THU + 🔥 MÓN BÁN CHẠY (TOP 5) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- BIỂU ĐỒ DOANH THU & SỐ ĐƠN (2 Cột) -->
        <div class="lg:col-span-2 bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
            
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-gray-900">
                        Doanh thu theo {{ $range === 'today' ? 'khung giờ' : 'ngày' }}
                    </h3>
                </div>

                <!-- Toggle Doanh thu | Số đơn -->
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl text-xs font-bold">
                    <button 
                        type="button" 
                        @click="toggleChartMode('revenue')"
                        class="px-2.5 py-1 rounded-lg transition-all cursor-pointer"
                        :class="chartMode === 'revenue' ? 'bg-white text-gray-900 shadow-xs font-black' : 'text-gray-600 hover:text-gray-900'"
                    >
                        Doanh thu
                    </button>
                    <button 
                        type="button" 
                        @click="toggleChartMode('orders')"
                        class="px-2.5 py-1 rounded-lg transition-all cursor-pointer"
                        :class="chartMode === 'orders' ? 'bg-white text-gray-900 shadow-xs font-black' : 'text-gray-600 hover:text-gray-900'"
                    >
                        Số đơn
                    </button>
                </div>
            </div>

            <!-- Canvas Biểu đồ -->
            <div class="h-64 relative">
                <canvas id="dashboardChart"></canvas>
            </div>

        </div>

        <!-- 🔥 MÓN BÁN CHẠY (1 Cột) -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
            
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                <h3 class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                    <span>🔥 Món bán chạy</span>
                </h3>
                <span class="text-[11px] text-gray-400 font-medium">Top 5 trong kỳ</span>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($topProducts as $index => $item)
                    <div class="py-2.5 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                        
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-slate-100 text-slate-700' : ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                                {{ $index + 1 }}
                            </span>
                            <img 
                                src="{{ $item->image_url }}" 
                                alt="{{ $item->product_name }}" 
                                class="w-10 h-10 rounded-xl object-cover border border-gray-200 shrink-0 bg-gray-50"
                            >
                            <div class="truncate">
                                <span class="font-bold text-xs text-gray-900 block truncate" title="{{ $item->product_name }}">
                                    {{ $item->product_name }}
                                </span>
                                <span class="text-[11px] text-gray-500 font-medium block">
                                    <strong class="text-gray-800">{{ $item->total_quantity }}</strong> suất · <span class="text-gray-400">{{ number_format((float) $item->total_revenue, 0, ',', '.') }} ₫</span>
                                </span>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-xs">
                        Chưa có dữ liệu bán hàng trong kỳ này.
                    </div>
                @endforelse
            </div>

        </div>

    </div>

    <!-- 6. BẢNG 5 ĐƠN GẦN NHẤT (GỌN GÀNG, ACTION-FIRST) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-2">
        
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-gray-900">5 Đơn Hàng Gần Nhất</h3>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 transition-colors">
                Xem tất cả đơn →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">Đơn</th>
                        <th class="px-4 py-3">Khách</th>
                        <th class="px-4 py-3">Món</th>
                        <th class="px-4 py-3 text-right">Tổng</th>
                        <th class="px-4 py-3">Trạng Thái</th>
                        <th class="px-4 py-3 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    @forelse($recentOrders as $order)
                        @php
                            $itemsSummary = $order->items->pluck('product_name')->implode(', ');
                            $isPaid = ($order->payment_status === 'paid');
                            
                            $rowModalPayload = [
                                'id' => $order->id,
                                'code' => $order->order_code,
                                'status' => $order->order_status,
                                'status_label' => $order->status_label,
                                'status_color' => $order->status_color,
                                'time' => $order->created_at ? $order->created_at->format('H:i · d/m/Y') : '',
                                'name' => $order->customer_name,
                                'phone' => $order->customer_phone,
                                'address' => $order->address,
                                'district' => $order->district,
                                'driver_note' => $order->driver_note,
                                'payment_method' => $order->payment_method_label,
                                'is_paid' => $isPaid,
                                'shipping_text' => (float) $order->shipping_fee === 0.0 ? 'Freeship' : 'Phí ship ' . number_format((float) $order->shipping_fee, 0, ',', '.') . ' ₫',
                                'total' => number_format((float) $order->total_amount, 0, ',', '.') . ' ₫',
                                'items' => $order->items->map(fn($item) => [
                                    'name' => $item->product_name,
                                    'qty' => $item->quantity,
                                    'sauce' => $item->sauce,
                                    'toppings' => $item->toppings,
                                    'price' => number_format((float) ($item->total_item_price ?: ($item->price * $item->quantity)), 0, ',', '.') . ' ₫'
                                ])
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openDetailModal({{ json_encode($rowModalPayload) }})">
                            
                            <!-- Đơn -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-black text-gray-900 font-mono text-xs block">#{{ $order->order_code }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $order->created_at ? $order->created_at->format('H:i - d/m') : '' }}</span>
                            </td>

                            <!-- Khách -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-gray-900 block text-xs">{{ $order->customer_name }}</span>
                                <span class="text-[11px] text-gray-500 font-semibold">{{ $order->district }}</span>
                            </td>

                            <!-- Món -->
                            <td class="px-4 py-3.5 max-w-[220px]">
                                <span class="font-semibold text-gray-800 block truncate" title="{{ $itemsSummary }}">
                                    {{ $itemsSummary }}
                                </span>
                            </td>

                            <!-- Tổng tiền -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <span class="font-black text-red-600 block text-xs">
                                    {{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫
                                </span>
                                <span class="text-[10px] text-gray-400">{{ $isPaid ? 'Đã CK' : 'Thu COD' }}</span>
                            </td>

                            <!-- Trạng thái -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span 
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border" 
                                    :class="orderStatuses[{{ $order->id }}]?.color || '{{ $order->status_color }}'"
                                    x-text="orderStatuses[{{ $order->id }}]?.label || '{{ $order->status_label }}'"
                                >
                                    {{ $order->status_label }}
                                </span>
                            </td>

                            <!-- Thao tác -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right" @click.stop>
                                <button 
                                    type="button" 
                                    @click="openDetailModal({{ json_encode($rowModalPayload) }})"
                                    class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition-colors cursor-pointer"
                                >
                                    Chi tiết ↗
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-400 text-xs">
                                Chưa có đơn hàng nào trong hệ thống.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- 7. REFACTORED ORDER DETAIL MODAL (ACTION-FIRST, 4 SECTIONS, WORKFLOW PIPELINE, AJAX STATE) -->
    <div 
        x-show="selectedOrder" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen p-3 sm:p-4 text-center">
            
            <!-- Backdrop -->
            <div 
                x-show="selectedOrder"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-xs" 
                @click="selectedOrder = null"
            ></div>

            <!-- Modal Panel (Width: 560-600px) -->
            <div 
                x-show="selectedOrder"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg sm:max-w-[580px] relative p-5 sm:p-6 space-y-4 text-xs z-50"
            >
                
                <!-- SECTION 1: HEADER -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-base sm:text-lg text-gray-900 font-mono" x-text="'#' + selectedOrder?.code"></span>
                        </div>
                        <span class="text-xs text-gray-400 font-mono block" x-text="selectedOrder?.time"></span>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border" :class="selectedOrder?.status_color" x-text="selectedOrder?.status_label"></span>
                        <button 
                            @click="selectedOrder = null" 
                            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold flex items-center justify-center transition-colors cursor-pointer text-sm"
                            title="Đóng (Esc)"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <!-- SECTION 2: KHÁCH & GIAO HÀNG -->
                <div class="p-3.5 sm:p-4 bg-gray-50/90 rounded-2xl border border-gray-200 space-y-3">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-black text-sm sm:text-base text-gray-900 tracking-wide uppercase" x-text="selectedOrder?.name"></span>
                            <a 
                                :href="'tel:' + selectedOrder?.phone" 
                                class="inline-flex items-center gap-1 font-mono font-black text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200/80 px-2.5 py-0.5 rounded-lg text-xs transition-colors"
                                title="Bấm gọi trực tiếp"
                            >
                                <span>📞</span>
                                <span x-text="selectedOrder?.phone"></span>
                            </a>
                        </div>

                        <!-- Fast Action Toolbar (Gọi - Chỉ đường - Copy C) -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            <a 
                                :href="'tel:' + selectedOrder?.phone" 
                                class="px-2.5 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition-colors flex items-center gap-1"
                                title="Gọi cho khách"
                            >
                                <span>📞</span>
                                <span>Gọi</span>
                            </a>
                            <a 
                                :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent((selectedOrder?.address || '') + ', ' + (selectedOrder?.district || '') + ', Hà Nội')" 
                                target="_blank" 
                                class="px-2.5 py-1 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] shadow-2xs transition-colors flex items-center gap-1"
                                title="Mở Google Maps chỉ đường"
                            >
                                <span>🗺️</span>
                                <span>Chỉ đường</span>
                            </a>
                            <button 
                                type="button" 
                                @click="copyShipperInfo(selectedOrder)"
                                class="px-2.5 py-1 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] shadow-2xs transition-colors flex items-center gap-1 cursor-pointer"
                                title="Sao chép thông tin gửi shipper (Phím C)"
                            >
                                <span>📋</span>
                                <span>Copy (C)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Địa chỉ giao hàng -->
                    <div class="bg-white p-3 rounded-xl border border-gray-200/90 shadow-2xs flex items-start gap-2 text-xs">
                        <span class="text-base shrink-0 leading-none">📍</span>
                        <div class="leading-relaxed">
                            <span class="font-black text-gray-900 text-sm" x-text="selectedOrder?.address"></span>
                            <span class="text-gray-400 font-bold mx-1">,</span>
                            <strong class="text-gray-800 font-bold text-sm" x-text="selectedOrder?.district"></strong>
                        </div>
                    </div>

                    <!-- Ghi chú tài xế -->
                    <template x-if="selectedOrder?.driver_note && selectedOrder.driver_note.trim() !== ''">
                        <div class="p-2.5 bg-amber-50 rounded-xl text-orange-950 text-xs italic font-medium border border-amber-200/90 flex items-start gap-1.5">
                            <span class="shrink-0 font-normal">📝</span>
                            <div>
                                <strong class="font-bold">Ghi chú:</strong> “<span x-text="selectedOrder.driver_note"></span>”
                            </div>
                        </div>
                    </template>

                </div>

                <!-- SECTION 3: MÓN ĐÃ ĐẶT -->
                <div class="space-y-2 border-t border-b border-gray-100 py-3">
                    <template x-for="(it, idx) in selectedOrder?.items" :key="idx">
                        <div class="flex justify-between items-start text-xs">
                            <div class="space-y-0.5">
                                <div class="font-bold text-gray-900 text-xs">
                                    <span class="font-mono font-black text-red-600" x-text="it.qty + '×'"></span>
                                    <span x-text="it.name"></span>
                                </div>
                                <div class="text-[11px] text-gray-500 font-medium" x-show="it.sauce || (it.toppings && it.toppings.length > 0)">
                                    <span x-show="it.sauce" x-text="'Sốt: ' + it.sauce"></span>
                                    <span x-show="it.sauce && it.toppings && it.toppings.length > 0"> · </span>
                                    <span x-show="it.toppings && it.toppings.length > 0" x-text="'Topping: ' + (Array.isArray(it.toppings) ? it.toppings.join(', ') : it.toppings)"></span>
                                </div>
                            </div>
                            <span class="font-bold font-mono text-gray-900 text-xs shrink-0 pl-2" x-text="it.price"></span>
                        </div>
                    </template>
                </div>

                <!-- SECTION 4: THANH TOÁN -->
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[11px] text-gray-500 font-medium">
                            <span x-text="selectedOrder?.payment_method"></span>
                            <span class="text-gray-300 mx-1">·</span>
                            <span x-text="selectedOrder?.shipping_text"></span>
                        </div>
                        <div class="pt-0.5">
                            <span 
                                class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold" 
                                :class="selectedOrder?.is_paid ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                x-text="selectedOrder?.is_paid ? '🟢 Đã thanh toán' : '🟡 Thu COD'"
                            ></span>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-bold">Tổng tiền</span>
                        <span class="text-xl sm:text-2xl font-black text-red-600 block leading-tight" x-text="selectedOrder?.total"></span>
                    </div>
                </div>

                <!-- SECTION 5: WORKFLOW PROGRESS PIPELINE -->
                <div class="flex items-center justify-between text-[11px] font-bold p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                    <div class="flex items-center gap-1.5" :class="selectedOrder?.status === 'pending' ? 'text-amber-800 font-black' : 'text-gray-500'">
                        <span class="w-2 h-2 rounded-full" :class="selectedOrder?.status === 'pending' ? 'bg-amber-500 ring-2 ring-amber-200 animate-pulse' : (['confirmed', 'preparing', 'processing', 'delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'bg-emerald-500' : 'bg-gray-400')"></span>
                        <span>Đã đặt</span>
                    </div>
                    <span class="text-gray-300 text-xs">→</span>

                    <div class="flex items-center gap-1.5" :class="['confirmed', 'preparing', 'processing'].includes(selectedOrder?.status) ? 'text-orange-800 font-black' : (['delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'text-gray-500' : 'text-gray-400')">
                        <span class="w-2 h-2 rounded-full" :class="['confirmed', 'preparing', 'processing'].includes(selectedOrder?.status) ? 'bg-orange-500 ring-2 ring-orange-200 animate-pulse' : (['delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'bg-emerald-500' : 'bg-gray-300')"></span>
                        <span>Đang làm</span>
                    </div>
                    <span class="text-gray-300 text-xs">→</span>

                    <div class="flex items-center gap-1.5" :class="['delivering', 'shipping'].includes(selectedOrder?.status) ? 'text-blue-800 font-black' : (selectedOrder?.status === 'completed' ? 'text-gray-500' : 'text-gray-400')">
                        <span class="w-2 h-2 rounded-full" :class="['delivering', 'shipping'].includes(selectedOrder?.status) ? 'bg-blue-500 ring-2 ring-blue-200 animate-pulse' : (selectedOrder?.status === 'completed' ? 'bg-emerald-500' : 'bg-gray-300')"></span>
                        <span>Đang giao</span>
                    </div>
                    <span class="text-gray-300 text-xs">→</span>

                    <div class="flex items-center gap-1.5" :class="selectedOrder?.status === 'completed' ? 'text-emerald-800 font-black' : 'text-gray-400'">
                        <span class="w-2 h-2 rounded-full" :class="selectedOrder?.status === 'completed' ? 'bg-emerald-500 ring-2 ring-emerald-200' : 'bg-gray-300'"></span>
                        <span>Hoàn thành</span>
                    </div>
                </div>

                <!-- SECTION 6: FOOTER CTA (AJAX 1-CHẠM) -->
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    
                    <div class="flex-[3]">
                        <template x-if="selectedOrder?.status === 'pending'">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedOrder.id, 'preparing', selectedOrder.code)"
                                class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <span>🍳 Nhận & Làm món</span>
                            </button>
                        </template>

                        <template x-if="selectedOrder?.status === 'confirmed'">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedOrder.id, 'preparing', selectedOrder.code)"
                                class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <span>🔥 Bắt đầu làm món</span>
                            </button>
                        </template>

                        <template x-if="['preparing', 'processing'].includes(selectedOrder?.status)">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedOrder.id, 'delivering', selectedOrder.code)"
                                class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <span>📦 Đóng gói & Đi giao</span>
                            </button>
                        </template>

                        <template x-if="['delivering', 'shipping'].includes(selectedOrder?.status)">
                            <button 
                                type="button" 
                                @click="updateOrderStatus(selectedOrder.id, 'completed', selectedOrder.code)"
                                class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <span>✅ Giao thành công</span>
                            </button>
                        </template>

                        <template x-if="selectedOrder?.status === 'completed'">
                            <div class="w-full py-2.5 rounded-xl bg-emerald-50 text-emerald-800 font-black text-xs text-center border border-emerald-200">
                                ✓ Đơn đã hoàn tất
                            </div>
                        </template>

                        <template x-if="selectedOrder?.status === 'cancelled'">
                            <div class="w-full py-2.5 rounded-xl bg-gray-100 text-gray-500 font-bold text-xs text-center">
                                ✕ Đơn đã huỷ
                            </div>
                        </template>
                    </div>

                    <!-- Nút phụ: Đóng modal -->
                    <button 
                        type="button" 
                        @click="selectedOrder = null" 
                        class="flex-1 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors cursor-pointer"
                    >
                        Đóng
                    </button>

                </div>

            </div>

        </div>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
