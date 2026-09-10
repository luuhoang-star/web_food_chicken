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
        urgentCount: {{ $urgentOrdersCount }},
        urgentThresholdMinutes: {{ $urgentThresholdMinutes }},
        cancelDialogOrder: null,
        cancelReasonText: 'Khách yêu cầu huỷ',
        
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
            const bgColor = isRevenue ? 'rgba(220, 38, 38, 0.06)' : 'rgba(37, 99, 235, 0.06)';
            
            // TỰ ĐỘNG SCALE TRỤC Y VỚI KHOẢNG ĐỆM 15-20% VÀ LÀM TRÒN MỐC PHÙ HỢP
            const maxVal = Math.max(...dataValues, isRevenue ? 50000 : 3);
            let suggestedMax;
            if (isRevenue) {
                const paddedMax = maxVal * 1.18;
                if (paddedMax <= 100000) {
                    suggestedMax = Math.ceil(paddedMax / 20000) * 20000;
                } else if (paddedMax <= 500000) {
                    suggestedMax = Math.ceil(paddedMax / 50000) * 50000;
                } else if (paddedMax <= 2000000) {
                    suggestedMax = Math.ceil(paddedMax / 100000) * 100000;
                } else {
                    suggestedMax = Math.ceil(paddedMax / 500000) * 500000;
                }
            } else {
                const paddedMax = maxVal * 1.2;
                if (paddedMax <= 10) {
                    suggestedMax = Math.ceil(paddedMax);
                } else if (paddedMax <= 50) {
                    suggestedMax = Math.ceil(paddedMax / 5) * 5;
                } else {
                    suggestedMax = Math.ceil(paddedMax / 10) * 10;
                }
            }

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
                            suggestedMax: suggestedMax,
                            grid: { color: '#f3f4f6' },
                            ticks: {
                                precision: 0,
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

                    if (this.selectedOrder && this.selectedOrder.id === orderId) {
                        this.selectedOrder.status = data.order_status;
                        this.selectedOrder.status_label = data.status_label;
                        this.selectedOrder.status_color = data.status_color;
                        this.selectedOrder.is_paid = data.is_paid;
                        this.selectedOrder.cancellation_reason = data.cancellation_reason;
                    }

                    if (newStatus === 'completed' || newStatus === 'cancelled') {
                        if (this.pendingCount > 0) this.pendingCount--;
                        if (this.urgentCount > 0) this.urgentCount--;
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

    <!-- 1. BỘ LỌC THỜI GIAN TINH GỌN (Segmented Pill Tab Bar) -->
    <div class="bg-white p-2.5 sm:p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 select-none">
        
        <!-- Segmented Tabs -->
        <div class="flex items-center gap-1 bg-slate-100/90 p-1 rounded-xl overflow-x-auto scrollbar-none text-xs font-bold">
            <a 
                href="{{ route('admin.dashboard', ['range' => 'today']) }}" 
                class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-all {{ $range === 'today' ? 'bg-white text-slate-900 shadow-2xs font-black' : 'text-slate-600 hover:text-slate-950' }}"
            >
                Hôm nay
            </a>
            <a 
                href="{{ route('admin.dashboard', ['range' => '7days']) }}" 
                class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-all {{ $range === '7days' ? 'bg-white text-slate-900 shadow-2xs font-black' : 'text-slate-600 hover:text-slate-950' }}"
            >
                7 ngày
            </a>
            <a 
                href="{{ route('admin.dashboard', ['range' => '30days']) }}" 
                class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-all {{ $range === '30days' ? 'bg-white text-slate-900 shadow-2xs font-black' : 'text-slate-600 hover:text-slate-950' }}"
            >
                30 ngày
            </a>
            <a 
                href="{{ route('admin.dashboard', ['range' => 'this_month']) }}" 
                class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-all {{ $range === 'this_month' ? 'bg-white text-slate-900 shadow-2xs font-black' : 'text-slate-600 hover:text-slate-950' }}"
            >
                Tháng này
            </a>
            <button 
                type="button" 
                @click="showCustomDate = !showCustomDate"
                class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-all cursor-pointer {{ $range === 'custom' ? 'bg-white text-slate-900 shadow-2xs font-black' : 'text-slate-600 hover:text-slate-950' }}"
            >
                Tùy chỉnh ▾
            </button>
        </div>

        <!-- Date Range Display Badge -->
        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 self-end md:self-auto">
            <span class="text-slate-400">📅</span>
            <span class="font-mono bg-slate-50 border border-slate-200/80 px-3 py-1 rounded-xl text-slate-800 shadow-2xs">{{ $dateDisplay }}</span>
        </div>

    </div>

    <!-- Custom Date Picker Collapse Box -->
    <div 
        x-show="showCustomDate" 
        x-transition 
        class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs"
        x-cloak
    >
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <input type="hidden" name="range" value="custom">
            <div class="flex items-center gap-2 font-bold text-slate-700">
                <span>Từ ngày:</span>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 font-mono outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <div class="flex items-center gap-2 font-bold text-slate-700">
                <span>Đến ngày:</span>
                <input type="date" name="to_date" value="{{ $toDate }}" class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 font-mono outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-black transition-colors cursor-pointer shadow-2xs">
                Lọc dữ liệu
            </button>
        </form>
    </div>

    <!-- 2. 4 THẺ KPI CHỈ SỐ CAO CẤP (CHUẨN SAAS/POS ANALYTICS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- CARD 1: DOANH THU THỰC NHẬN -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Doanh thu {{ $range === 'today' ? 'hôm nay' : 'kỳ này' }}
                </span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-sm font-bold shadow-2xs">
                    💰
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight block">
                    {{ number_format($periodRevenue, 0, ',', '.') }} <span class="text-lg font-black text-rose-600">₫</span>
                </span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                @if($revenueGrowthPercent !== null)
                    <span class="font-bold flex items-center gap-1 {{ $revenueGrowthPercent >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        <span>{{ $revenueGrowthPercent >= 0 ? '↗' : '↘' }}</span>
                        <span>{{ abs($revenueGrowthPercent) }}% so với {{ $compareLabel }}</span>
                    </span>
                @else
                    <span class="text-slate-400 font-medium">Doanh thu thực tế</span>
                @endif
                <span class="text-slate-400 font-mono text-[10px]">{{ $range === 'today' ? 'Realtime' : 'Tổng kết' }}</span>
            </div>
        </div>

        <!-- CARD 2: TỔNG SỐ ĐƠN HÀNG -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Tổng đơn {{ $range === 'today' ? 'hôm nay' : 'kỳ này' }}
                </span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-sm font-bold shadow-2xs">
                    📦
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight block">
                    {{ $periodOrdersCount }} <span class="text-base font-bold text-slate-400">đơn</span>
                </span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="font-medium text-slate-600">
                    <strong class="text-emerald-600 font-bold">{{ $completedOrdersCount }}</strong> xong · <span class="text-slate-400">{{ $cancelledOrdersCount }} huỷ</span>
                </span>
                <span class="text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded-md text-[10px]">
                    {{ $todayStats['dishes_count'] ?? 0 }} món
                </span>
            </div>
        </div>

        <!-- CARD 3: ĐƠN CẦN XỬ LÝ (ACTIONABLE PRIORITY - CÓ NGƯỠNG THỜI GIAN) -->
        <div 
            class="bg-white p-4 sm:p-5 rounded-2xl border-2 shadow-xs transition-all flex flex-col justify-between space-y-3"
            :class="urgentCount > 0 ? 'border-red-500 bg-gradient-to-br from-red-50/60 via-white to-orange-50/30 ring-2 ring-red-500/20' : (pendingCount > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200/80')"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider" :class="urgentCount > 0 ? 'text-red-700 font-black' : (pendingCount > 0 ? 'text-amber-800 font-black' : 'text-slate-500')">
                    Đơn cần xử lý ngay
                </span>
                <div class="relative">
                    <div class="w-8 h-8 rounded-xl border flex items-center justify-center text-sm font-bold shadow-2xs" :class="urgentCount > 0 ? 'bg-red-50 text-red-600 border-red-100' : 'bg-amber-50 text-amber-600 border-amber-100'">
                        ⚡
                    </div>
                    <!-- Chỉ nhấp nháy đỏ báo động khi có đơn chờ quá ngưỡng thời gian (10 phút) -->
                    <span x-show="urgentCount > 0" class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-red-600 animate-ping"></span>
                </div>
            </div>
            <div>
                <span 
                    class="text-2xl sm:text-3xl font-black tracking-tight block"
                    :class="urgentCount > 0 ? 'text-red-600' : (pendingCount > 0 ? 'text-slate-900' : 'text-slate-900')"
                    x-text="pendingCount + ' đơn'"
                >
                    {{ $pendingOrdersCount }} đơn
                </span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <template x-if="urgentCount > 0">
                    <span class="font-black text-red-600 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse inline-block"></span>
                        <span x-text="urgentCount + ' đơn chờ > ' + urgentThresholdMinutes + 'p'"></span>
                    </span>
                </template>
                <template x-if="urgentCount === 0 && pendingCount > 0">
                    <span class="text-amber-700 font-bold flex items-center gap-1">
                        <span>🕒</span>
                        <span>Đơn mới trong hạn bình thường</span>
                    </span>
                </template>
                <template x-if="pendingCount === 0">
                    <span class="text-emerald-600 font-bold flex items-center gap-1">
                        <span>✓</span>
                        <span>Bếp đã hết đơn chờ</span>
                    </span>
                </template>
                <span class="text-slate-400 font-medium text-[10px]">Ưu tiên số 1</span>
            </div>
        </div>

        <!-- CARD 4: GIÁ TRỊ ĐƠN TB & TỶ LỆ HOÀN THÀNH -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Giá trị TB / Đơn (AOV)
                </span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-sm font-bold shadow-2xs">
                    🎯
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight block">
                    {{ number_format($avgOrderValue, 0, ',', '.') }} <span class="text-lg font-bold text-slate-400">₫</span>
                </span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-600 font-medium truncate" title="Tỷ lệ hoàn thành: {{ $completedOrdersCount }}/{{ $periodOrdersCount }} đơn ({{ $completionRate }}%)">
                    Tỷ lệ hoàn thành: <strong class="text-emerald-600 font-bold">{{ $completedOrdersCount }}/{{ $periodOrdersCount }} đơn</strong> ({{ $completionRate }}%)
                </span>
                <span class="text-slate-400 text-[10px] shrink-0">Hiệu suất</span>
            </div>
        </div>

    </div>

    <!-- 3. KHU VỰC QUAN TRỌNG: 🔴 ĐƠN CẦN XỬ LÝ NGAY (POS TICKET CARDS) -->
    <div id="urgent-queue" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 space-y-4">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
            <div class="flex items-center gap-2.5">
                <h2 class="text-sm sm:text-base font-black text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full inline-block" :class="urgentCount > 0 ? 'bg-red-600 animate-ping' : (pendingCount > 0 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                    <span>Đơn hàng đang chờ xử lý</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black" :class="urgentCount > 0 ? 'bg-red-100 text-red-700' : (pendingCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600')" x-text="pendingCount + ' đơn'">
                    </span>
                    <template x-if="urgentCount > 0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-red-600 text-white animate-pulse" x-text="urgentCount + ' đơn quá ' + urgentThresholdMinutes + 'p!'"></span>
                    </template>
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
                        $groupedActionItems = [];
                        foreach ($order->items as $it) {
                            $sauce = trim((string) ($it->sauce ?? ''));
                            $cleanSauce = preg_replace('/^sốt\s+/iu', '', $sauce);
                            $hasSauceInTitle = ($cleanSauce !== '' && mb_stripos($it->product_name, $cleanSauce) !== false);
                            $sauceLabel = (!$hasSauceInTitle && $cleanSauce !== '') ? " (Sốt {$cleanSauce})" : '';
                            $dishName = $it->product_name . $sauceLabel;
                            $groupedActionItems[$dishName] = ($groupedActionItems[$dishName] ?? 0) + (int) $it->quantity;
                        }
                        $isPaid = ($order->payment_status === 'paid');
                        $minutesAgo = $order->created_at ? (int) abs(now()->diffInMinutes($order->created_at)) : 0;
                        $timeWait = $minutesAgo < 1 ? 'Vừa xong' : ($minutesAgo < 60 ? $minutesAgo . 'p trước' : (int) ($minutesAgo / 60) . 'h trước');
                        $isUrgent = ($order->order_status === 'pending' && $minutesAgo >= $urgentThresholdMinutes);
                        
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
                            'cancellation_reason' => $order->cancellation_reason,
                            'items' => $order->items->map(fn($item) => [
                                'name' => $item->product_name,
                                'qty' => $item->quantity,
                                'sauce' => $item->sauce,
                                'toppings' => $item->formatted_toppings,
                                'price' => number_format((float) ($item->total_item_price ?: ($item->price * $item->quantity)), 0, ',', '.') . ' ₫'
                            ])
                        ];
                    @endphp
                    <div 
                        class="p-4 rounded-2xl border transition-all space-y-3 flex flex-col justify-between shadow-2xs {{ $isUrgent ? 'border-red-400 bg-red-50/30 hover:bg-white' : 'border-slate-200/90 bg-slate-50/40 hover:bg-white hover:border-slate-300' }}"
                        x-show="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') !== 'completed' && (orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') !== 'cancelled'"
                    >
                        
                        <div class="space-y-2.5">
                            <!-- Top: Mã đơn & Trạng thái -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono font-black text-xs text-slate-900 block">
                                        #{{ $order->order_code }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-medium {{ $isUrgent ? 'text-red-600 font-bold' : '' }}">⏱️ {{ $timeWait }}</span>
                                    @if($isUrgent)
                                        <span class="px-1.5 py-0.2 rounded bg-red-600 text-white font-black text-[9px] uppercase animate-pulse">Quá {{ $urgentThresholdMinutes }}p</span>
                                    @endif
                                </div>
                                <span 
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full border shadow-2xs" 
                                    :class="orderStatuses[{{ $order->id }}]?.color || '{{ $order->status_color }}'"
                                    x-text="orderStatuses[{{ $order->id }}]?.label || '{{ $order->status_label }}'"
                                >
                                </span>
                            </div>

                            <!-- Khách hàng & SĐT & Địa chỉ -->
                            <div class="text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <strong class="text-slate-900 font-black text-xs uppercase">{{ $order->customer_name }}</strong>
                                    <a href="tel:{{ $order->customer_phone }}" class="text-red-600 font-mono font-black text-[11px] hover:underline" @click.stop title="Gọi cho khách">
                                        📞 {{ $order->customer_phone }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-700 bg-white p-2.5 rounded-xl border border-slate-200/60 font-medium leading-tight shadow-2xs">
                                    <span class="text-red-500">📍</span>
                                    <span class="font-bold text-slate-900">{{ $order->address }}</span>, <strong class="text-slate-800">{{ $order->district }}</strong>
                                </div>
                            </div>

                            <!-- Món ăn dạng danh sách rõ ràng -->
                            <div class="space-y-1 py-1 bg-white/70 p-2 rounded-xl border border-slate-100">
                                @foreach($groupedActionItems as $dishName => $qty)
                                    <div class="flex items-center gap-1.5 text-xs">
                                        <span class="px-1.5 py-0.2 rounded font-mono font-black text-[10px] bg-red-50 text-red-700 border border-red-200 shrink-0">
                                            ×{{ $qty }}
                                        </span>
                                        <span class="font-bold text-slate-900 truncate" title="{{ $dishName }}">{{ $dishName }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Tiền thu -->
                            <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200/60">
                                <span class="text-slate-500 font-medium">Tổng thu:</span>
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

                        <!-- 1 PRIMARY CTA DUY NHẤT DỰA VÀO TRẠNG THÁI (AJAX 1-CHẠM) + NÚT MẮT TOOLTIP -->
                        <div class="pt-2 border-t border-dashed border-slate-200 flex items-center gap-2">
                            
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

                            <!-- Nút xem chi tiết có Tooltip và Nhãn rõ ràng -->
                            <button 
                                type="button" 
                                @click="openDetailModal({{ json_encode($modalPayload) }})"
                                class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition-colors cursor-pointer shadow-2xs flex items-center gap-1"
                                title="Xem chi tiết đơn (#{{ $order->order_code }})"
                            >
                                <span>👁️</span>
                                <span class="hidden sm:inline">Chi tiết</span>
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="py-6 text-center text-xs font-bold text-slate-500 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                🎉 Tuyệt vời! Hiện không có đơn nào đang chờ xử lý. Bếp đã hoàn thành tất cả đơn hàng.
            </div>
        @endif

    </div>

    <!-- 4. 2-COLUMN LAYOUT: BIỂU ĐỒ DOANH THU + 🔥 MÓN BÁN CHẠY (TOP 5) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- BIỂU ĐỒ DOANH THU & SỐ ĐƠN (2 Cột) -->
        <div class="lg:col-span-2 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">
                        Doanh thu theo {{ $range === 'today' ? 'khung giờ' : 'ngày' }}
                    </h3>
                </div>

                <!-- Toggle Doanh thu | Số đơn -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                    <button 
                        type="button" 
                        @click="toggleChartMode('revenue')"
                        class="px-2.5 py-1 rounded-lg transition-all cursor-pointer"
                        :class="chartMode === 'revenue' ? 'bg-white text-slate-900 shadow-2xs font-black' : 'text-slate-600 hover:text-slate-900'"
                    >
                        Doanh thu
                    </button>
                    <button 
                        type="button" 
                        @click="toggleChartMode('orders')"
                        class="px-2.5 py-1 rounded-lg transition-all cursor-pointer"
                        :class="chartMode === 'orders' ? 'bg-white text-slate-900 shadow-2xs font-black' : 'text-slate-600 hover:text-slate-900'"
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
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                    <span>🔥 Món bán chạy</span>
                </h3>
                <span class="text-[11px] text-slate-400 font-medium">Top 5 trong kỳ</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($topProducts as $index => $item)
                    <div class="py-2.5 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                        
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs shadow-2xs {{ $index === 0 ? 'bg-amber-100 text-amber-700 ring-1 ring-amber-300' : ($index === 1 ? 'bg-slate-100 text-slate-700 ring-1 ring-slate-300' : ($index === 2 ? 'bg-orange-100 text-orange-700 ring-1 ring-orange-300' : 'bg-slate-100 text-slate-500')) }}">
                                {{ $index + 1 }}
                            </span>
                            <img 
                                src="{{ $item->image_url }}" 
                                alt="{{ $item->product_name }}" 
                                class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0 bg-slate-50 shadow-2xs"
                            >
                            <div class="truncate">
                                <span class="font-bold text-xs text-slate-900 block truncate" title="{{ $item->product_name }}">
                                    {{ $item->product_name }}
                                </span>
                                <span class="text-[11px] text-slate-500 font-medium block">
                                    <strong class="text-slate-800">{{ $item->total_quantity }}</strong> suất · <span class="text-slate-400">{{ number_format((float) $item->total_revenue, 0, ',', '.') }} ₫</span>
                                </span>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Chưa có dữ liệu bán hàng trong kỳ này.
                    </div>
                @endforelse
            </div>

        </div>

    </div>

    <!-- 5. BẢNG 5 ĐƠN GẦN NHẤT (GỌN GÀNG, ACTION-FIRST) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-2">
        
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-slate-900">5 Đơn Hàng Gần Nhất</h3>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 transition-colors flex items-center gap-1">
                <span>Xem tất cả đơn</span>
                <span>→</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[760px]">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider text-[10px] font-black border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 w-[110px] whitespace-nowrap">Đơn</th>
                        <th class="px-4 py-3 w-[150px] whitespace-nowrap">Khách</th>
                        <th class="px-4 py-3 min-w-[200px]">Món</th>
                        <th class="px-4 py-3 w-[110px] text-right whitespace-nowrap">Tổng Thu</th>
                        <th class="px-4 py-3 w-[165px] text-center whitespace-nowrap">Trạng Thái</th>
                        <th class="px-4 py-3 w-[90px] text-right whitespace-nowrap">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($recentOrders as $order)
                        @php
                            $groupedRecentItems = [];
                            foreach ($order->items as $it) {
                                $sauce = trim((string) ($it->sauce ?? ''));
                                $cleanSauce = preg_replace('/^sốt\s+/iu', '', $sauce);
                                $hasSauceInTitle = ($cleanSauce !== '' && mb_stripos($it->product_name, $cleanSauce) !== false);
                                $sauceLabel = (!$hasSauceInTitle && $cleanSauce !== '') ? " (Sốt {$cleanSauce})" : '';
                                $dishName = $it->product_name . $sauceLabel;
                                $groupedRecentItems[$dishName] = ($groupedRecentItems[$dishName] ?? 0) + (int) $it->quantity;
                            }
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
                                'cancellation_reason' => $order->cancellation_reason,
                                'items' => $order->items->map(fn($item) => [
                                    'name' => $item->product_name,
                                    'qty' => $item->quantity,
                                    'sauce' => $item->sauce,
                                    'toppings' => $item->formatted_toppings,
                                    'price' => number_format((float) ($item->total_item_price ?: ($item->price * $item->quantity)), 0, ',', '.') . ' ₫'
                                ])
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer" @click="openDetailModal({{ json_encode($rowModalPayload) }})">
                            
                            <!-- Đơn -->
                            <td class="px-4 py-3.5 whitespace-nowrap align-middle">
                                <span class="font-black text-slate-900 font-mono text-xs block">#{{ $order->order_code }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $order->created_at ? $order->created_at->format('H:i - d/m') : '' }}</span>
                            </td>

                            <!-- Khách -->
                            <td class="px-4 py-3.5 whitespace-nowrap align-middle">
                                <span class="font-bold text-slate-900 block text-xs truncate max-w-[140px]" title="{{ $order->customer_name }}">{{ $order->customer_name }}</span>
                                <span class="text-[11px] text-slate-500 font-medium">{{ $order->district }}</span>
                            </td>

                            <!-- Món -->
                            <td class="px-4 py-3.5 align-middle">
                                <div class="space-y-1">
                                    @foreach($groupedRecentItems as $dishName => $qty)
                                        <div class="flex items-center gap-1.5 text-xs">
                                            <span class="px-1.5 py-0.2 rounded font-mono font-black text-[10px] bg-red-50 text-red-700 border border-red-200 shrink-0">
                                                ×{{ $qty }}
                                            </span>
                                            <span class="font-bold text-slate-900 truncate max-w-[220px]" title="{{ $dishName }}">{{ $dishName }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Tổng tiền -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right align-middle">
                                <span class="font-black text-red-600 block text-xs">
                                    {{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫
                                </span>
                                <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold mt-0.5 {{ $isPaid ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                    {{ $isPaid ? '💳 Đã CK' : '💵 Thu COD' }}
                                </span>
                            </td>

                            <!-- Trạng thái (Dropdown đổi nhanh kèm Confirm Dialog, Khóa khi đã Hoàn thành / Đã huỷ) -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-center align-middle" @click.stop>
                                <template x-if="['completed', 'cancelled'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}')">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-black border shadow-2xs"
                                        :class="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'completed' ? 'bg-emerald-50 text-emerald-900 border-emerald-300' : 'bg-rose-50 text-rose-900 border-rose-300'"
                                        :title="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'completed' ? 'Đơn hàng đã hoàn tất (Đã chốt, không đổi trạng thái)' : 'Đơn hàng đã huỷ (Đã chốt, không đổi trạng thái)'"
                                    >
                                        <span class="text-[11px]">🔒</span>
                                        <span x-text="(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'completed' ? '✅ Đã giao xong' : '❌ Đã hủy đơn'"></span>
                                    </div>
                                </template>

                                <template x-if="!['completed', 'cancelled'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}')">
                                    <div class="relative inline-block w-full max-w-[155px] text-left group">
                                        <select 
                                            :value="orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}'"
                                            @change="handleDropdownStatusChange({{ $order->id }}, $event, '{{ $order->order_code }}', '{{ $order->order_status }}')"
                                            class="w-full pl-2.5 pr-6 py-1 rounded-xl text-xs font-black border shadow-2xs appearance-none cursor-pointer focus:outline-none focus:ring-2 transition-all font-sans"
                                            :class="{
                                                'bg-amber-50 text-amber-900 border-amber-300 hover:bg-amber-100 focus:ring-amber-400': (orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}') === 'pending',
                                                'bg-orange-50 text-orange-950 border-orange-300 hover:bg-orange-100 focus:ring-orange-400': ['confirmed', 'preparing', 'processing'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}'),
                                                'bg-blue-50 text-blue-950 border-blue-300 hover:bg-blue-100 focus:ring-blue-400': ['delivering', 'shipping'].includes(orderStatuses[{{ $order->id }}]?.status || '{{ $order->order_status }}'),
                                            }"
                                            title="Bấm vào để chọn đổi trạng thái đơn hàng (Có xác nhận với trạng thái cuối)"
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

                            <!-- Thao tác -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right align-middle" @click.stop>
                                <button 
                                    type="button" 
                                    @click="openDetailModal({{ json_encode($rowModalPayload) }})"
                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer shadow-2xs"
                                    title="Xem chi tiết đơn (#{{ $order->order_code }})"
                                >
                                    Chi tiết ↗
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400 text-xs">
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
                class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg sm:max-w-[580px] relative p-5 sm:p-6 space-y-4 text-xs z-50 border border-slate-200/80"
            >
                
                <!-- SECTION 1: HEADER -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-base sm:text-lg text-slate-900 font-mono" x-text="'#' + selectedOrder?.code"></span>
                        </div>
                        <span class="text-xs text-slate-400 font-mono block" x-text="selectedOrder?.time"></span>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-bold px-3 py-1 rounded-full border shadow-2xs" :class="selectedOrder?.status_color" x-text="selectedOrder?.status_label"></span>
                        <button 
                            @click="selectedOrder = null" 
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 font-bold flex items-center justify-center transition-colors cursor-pointer text-sm"
                            title="Đóng (Esc)"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <!-- SECTION 2: KHÁCH HÀNG, SỐ ĐIỆN THOẠI & ĐỊA CHỈ (RÕ RÀNG, TÁCH BẠCH) -->
                <div class="bg-slate-50/90 rounded-2xl border border-slate-200/80 p-3.5 sm:p-4 space-y-3">
                    
                    <!-- 2 Cột: Khách hàng & Số điện thoại -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <!-- Khách hàng -->
                        <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider flex items-center gap-1.5">
                                <span>👤</span>
                                <span>Khách hàng</span>
                            </span>
                            <span class="font-black text-sm sm:text-base text-slate-900 block truncate" x-text="selectedOrder?.name"></span>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider flex items-center gap-1.5">
                                <span>📞</span>
                                <span>Số điện thoại (Gọi ngay)</span>
                            </span>
                            <a 
                                :href="'tel:' + selectedOrder?.phone" 
                                class="font-mono font-black text-sm sm:text-base text-red-600 hover:text-red-700 hover:underline block truncate flex items-center gap-1"
                                title="Bấm để gọi ngay"
                            >
                                <span x-text="selectedOrder?.phone"></span>
                                <span class="text-xs font-sans text-red-500 font-semibold">(Bấm gọi ↗)</span>
                            </a>
                        </div>
                    </div>

                    <!-- Địa chỉ giao nhận -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider flex items-center gap-1.5">
                            <span>📍</span>
                            <span>Địa chỉ giao hàng</span>
                        </span>
                        <div class="text-xs sm:text-sm text-slate-900 leading-relaxed font-bold">
                            <span x-text="selectedOrder?.address"></span><template x-if="selectedOrder?.district"><span class="text-slate-600 font-semibold" x-text="', ' + selectedOrder?.district"></span></template>
                        </div>
                    </div>

                    <!-- Ghi chú tài xế -->
                    <template x-if="selectedOrder?.driver_note && selectedOrder.driver_note.trim() !== ''">
                        <div class="p-2.5 bg-amber-50 rounded-xl text-amber-950 text-xs font-medium border border-amber-200/90 flex items-start gap-2">
                            <span class="shrink-0 text-base">📝</span>
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-black text-amber-800 uppercase tracking-wide block">Ghi chú từ khách:</span>
                                <p class="font-bold italic" x-text="'“' + selectedOrder.driver_note + '”'"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Nhóm 3 nút hành động (Gọi khách là NÚT CHÍNH to bản, Chỉ đường & Copy là 2 ICON BUTTON nhỏ) -->
                    <div class="flex items-center gap-2 pt-1 border-t border-slate-200/80">
                        <!-- Nút Gọi khách (Nút chính to bản, ưu tiên hàng đầu) -->
                        <a 
                            :href="'tel:' + selectedOrder?.phone" 
                            class="flex-1 py-2.5 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs transition-all flex items-center justify-center gap-2"
                        >
                            <span class="text-sm">📞</span>
                            <span>Gọi khách ngay</span>
                            <span class="font-mono text-[11px] opacity-90 hidden sm:inline" x-text="'(' + selectedOrder?.phone + ')'"></span>
                        </a>

                        <!-- Nút Chỉ đường (Google Maps Icon Pin đa màu chính thức, nhận diện tức thì) -->
                        <a 
                            :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent((selectedOrder?.address || '') + ', ' + (selectedOrder?.district || '') + ', Hà Nội')" 
                            target="_blank" 
                            class="p-2.5 rounded-xl bg-white hover:bg-red-50 text-slate-700 border border-slate-200 hover:border-red-300 transition-all flex items-center justify-center shadow-2xs cursor-pointer group"
                            title="Mở Google Maps chỉ đường"
                        >
                            <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 92.3 132.3">
                                <path fill="#1a73e8" d="M60.2 2.2C55.8.8 51 0 46.1 0 32 0 19.3 6.4 10.8 16.5l21.8 18.3L60.2 2.2z"/>
                                <path fill="#ea4335" d="M10.8 16.5C4.1 24.5 0 34.9 0 46.1c0 8.7 1.7 15.7 4.6 22l28-33.3-21.8-18.3z"/>
                                <path fill="#4285f4" d="M46.2 28.5c9.8 0 17.7 7.9 17.7 17.7 0 4.3-1.6 8.3-4.2 11.4 0 0 13.9-16.6 28-33.3C80.8 13.6 69.4 4.8 56.1 1.2L32.6 34.8c3.3-3.9 8.1-6.3 13.6-6.3z"/>
                                <path fill="#fbbc04" d="M46.2 63.8c-9.8 0-17.7-7.9-17.7-17.7 0-4.3 1.5-8.3 4.1-11.3l-28 33.3c4.8 10.6 12.8 19.2 21 29.9l34.1-40.5c-3.3 3.9-8.1 6.3-13.5 6.3z"/>
                                <path fill="#34a853" d="M59.6 98c15.2-23.7 32.7-33.8 32.7-51.9 0-7.8-1.9-15.1-5.1-21.6l-48.6 57.8c2.6 3.4 5.3 7.1 8 11.2 7.2 11 5.2 17.7 11.5 17.7 6.3 0 4.3-6.7 11.5-17.7z"/>
                            </svg>
                        </a>

                        <!-- Nút Copy (Icon Button nhỏ, sắc nét) -->
                        <button 
                            type="button" 
                            @click="copyShipperInfo(selectedOrder)" 
                            class="p-2.5 rounded-xl bg-white hover:bg-purple-50 text-slate-700 border border-slate-200 hover:border-purple-300 transition-all flex items-center justify-center shadow-2xs cursor-pointer group"
                            title="Sao chép thông tin gửi Shipper (Phím C)"
                        >
                            <svg class="w-5 h-5 text-slate-600 group-hover:text-purple-700 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                    </div>

                </div>

                <!-- SECTION 3: MÓN ĐÃ ĐẶT -->
                <div class="space-y-2 border-t border-b border-slate-100 py-3">
                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Danh sách món đã đặt:</span>
                    <template x-for="(it, idx) in selectedOrder?.items" :key="idx">
                        <div class="flex justify-between items-start text-xs py-1 first:pt-0 last:pb-0">
                            <div class="space-y-0.5">
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <span class="font-mono font-black text-red-600 px-1.5 py-0.2 bg-red-50 border border-red-200 rounded" x-text="it.qty + '×'"></span>
                                    <span x-text="it.name"></span>
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium pl-6" x-show="(it.sauce && !it.name.toLowerCase().includes(it.sauce.toLowerCase().replace(/^sốt\s+/, ''))) || (it.toppings && it.toppings.length > 0)">
                                    <span x-show="it.sauce && !it.name.toLowerCase().includes(it.sauce.toLowerCase().replace(/^sốt\s+/, ''))" x-text="it.sauce.toLowerCase().startsWith('sốt') ? it.sauce : 'Sốt ' + it.sauce"></span>
                                    <span x-show="(it.sauce && !it.name.toLowerCase().includes(it.sauce.toLowerCase().replace(/^sốt\s+/, ''))) && it.toppings && it.toppings.length > 0"> · </span>
                                    <span x-show="it.toppings && it.toppings.length > 0" x-text="'Topping: ' + (Array.isArray(it.toppings) ? it.toppings.join(', ') : it.toppings)"></span>
                                </div>
                            </div>
                            <span class="font-bold text-slate-900 text-xs shrink-0 pl-2" x-text="it.price"></span>
                        </div>
                    </template>
                </div>

                <!-- SECTION 4: THANH TOÁN -->
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[11px] text-slate-500 font-medium">
                            <span x-text="selectedOrder?.payment_method"></span>
                            <span class="text-slate-300 mx-1">·</span>
                            <span x-text="selectedOrder?.shipping_text"></span>
                        </div>
                        <div class="pt-0.5">
                            <span 
                                class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold shadow-2xs" 
                                :class="selectedOrder?.is_paid ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                x-text="selectedOrder?.is_paid ? '🟢 Đã thanh toán' : '🟡 Thu COD'"
                            ></span>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Tổng tiền</span>
                        <span class="text-xl sm:text-2xl font-black text-red-600 block leading-tight" x-text="selectedOrder?.total"></span>
                    </div>
                </div>

                <!-- CẢNH BÁO / LÝ DO HUỶ ĐƠN NẾU CÓ -->
                <template x-if="selectedOrder?.status === 'cancelled' && selectedOrder?.cancellation_reason">
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-xs space-y-1">
                        <span class="text-[10px] font-black uppercase text-rose-800 tracking-wider flex items-center gap-1">
                            <span>❌</span>
                            <span>Lý do huỷ đơn:</span>
                        </span>
                        <p class="font-bold text-rose-900" x-text="selectedOrder.cancellation_reason"></p>
                    </div>
                </template>

                <!-- SECTION 5: WORKFLOW PROGRESS PIPELINE (CẢI TIẾN: NỐI XANH, HIGHLIGHT BƯỚC HIỆN TẠI) -->
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <!-- Bước 1: Đã đặt -->
                        <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg transition-all"
                            :class="selectedOrder?.status === 'pending' ? 'bg-amber-100/80 text-amber-900 ring-1 ring-amber-400 font-black' : (['confirmed', 'preparing', 'processing', 'delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'text-emerald-700' : 'text-slate-400')"
                        >
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :class="selectedOrder?.status === 'pending' ? 'bg-amber-500 ring-4 ring-amber-300/40 animate-pulse' : (['confirmed', 'preparing', 'processing', 'delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'bg-emerald-500' : 'bg-slate-300')"></span>
                            <span>Đã đặt</span>
                        </div>

                        <!-- Đường nối 1 -->
                        <div class="flex-1 h-0.5 mx-1 rounded-full" :class="['confirmed', 'preparing', 'processing', 'delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'bg-emerald-500' : 'bg-slate-200'"></div>

                        <!-- Bước 2: Đang làm -->
                        <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg transition-all"
                            :class="['confirmed', 'preparing', 'processing'].includes(selectedOrder?.status) ? 'bg-orange-100/80 text-orange-950 ring-1 ring-orange-400 font-black' : (['delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'text-emerald-700' : 'text-slate-400')"
                        >
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :class="['confirmed', 'preparing', 'processing'].includes(selectedOrder?.status) ? 'bg-orange-500 ring-4 ring-orange-300/40 animate-pulse' : (['delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'bg-emerald-500' : 'bg-slate-300')"></span>
                            <span>Đang làm</span>
                        </div>

                        <!-- Đường nối 2 -->
                        <div class="flex-1 h-0.5 mx-1 rounded-full" :class="['delivering', 'shipping', 'completed'].includes(selectedOrder?.status) ? 'bg-emerald-500' : 'bg-slate-200'"></div>

                        <!-- Bước 3: Đang giao -->
                        <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg transition-all"
                            :class="['delivering', 'shipping'].includes(selectedOrder?.status) ? 'bg-blue-100/80 text-blue-950 ring-1 ring-blue-400 font-black' : (selectedOrder?.status === 'completed' ? 'text-emerald-700' : 'text-slate-400')"
                        >
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :class="['delivering', 'shipping'].includes(selectedOrder?.status) ? 'bg-blue-500 ring-4 ring-blue-300/40 animate-pulse' : (selectedOrder?.status === 'completed' ? 'bg-emerald-500' : 'bg-slate-300')"></span>
                            <span>Đang giao</span>
                        </div>

                        <!-- Đường nối 3 -->
                        <div class="flex-1 h-0.5 mx-1 rounded-full" :class="selectedOrder?.status === 'completed' ? 'bg-emerald-500' : 'bg-slate-200'"></div>

                        <!-- Bước 4: Hoàn thành -->
                        <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg transition-all"
                            :class="selectedOrder?.status === 'completed' ? 'bg-emerald-100/80 text-emerald-950 ring-1 ring-emerald-400 font-black' : 'text-slate-400'"
                        >
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :class="selectedOrder?.status === 'completed' ? 'bg-emerald-500 ring-4 ring-emerald-300/40' : 'bg-slate-300'"></span>
                            <span>Xong</span>
                        </div>
                    </div>
                </div>

                <!-- SECTION 6: FOOTER CTA (AJAX 1-CHẠM + NÚT HUỶ ĐƠN) -->
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                    
                    <div class="flex-1">
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
                            <div class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs text-center">
                                ✕ Đơn đã huỷ
                            </div>
                        </template>
                    </div>

                    <!-- Nút Huỷ đơn (Chỉ hiện khi đơn chưa hoàn thành hoặc chưa huỷ) -->
                    <template x-if="selectedOrder && !['completed', 'cancelled'].includes(selectedOrder?.status)">
                        <button 
                            type="button" 
                            @click="openCancelDialog(selectedOrder)" 
                            class="py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-colors cursor-pointer"
                            title="Huỷ đơn hàng này"
                        >
                            ✕ Huỷ đơn
                        </button>
                    </template>

                    <!-- Nút phụ: Đóng modal -->
                    <button 
                        type="button" 
                        @click="selectedOrder = null" 
                        class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer"
                    >
                        Đóng
                    </button>

                </div>

            </div>

        </div>
    </div>

    <!-- 8. DIALOG CHỌN LÝ DO HUỶ ĐƠN HÀNG (MODAL CON AN TOÀN & TIỆN LỢI) -->
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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
