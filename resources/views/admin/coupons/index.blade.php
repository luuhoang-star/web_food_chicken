@extends('layouts.admin')

@section('title', 'Quản Lý Mã Giảm Giá & Voucher')
@section('page_title', '🎟️ Quản Lý Mã Giảm Giá & Voucher')

@section('content')
<div 
    class="space-y-5 pb-20" 
    x-data="{
        searchQuery: '',
        statusFilter: 'all',
        typeFilter: 'all',
        toastMessage: '',
        showDrawer: false,
        isEditing: false,
        activeMenuId: null,

        // Form state
        formId: null,
        formCode: '',
        formName: '',
        formType: 'fixed',
        formValue: '',
        formMinOrder: '',
        formMaxDiscount: '',
        formUsageLimit: '',
        formExpiresAt: '',
        formIsActive: true,

        coupons: {{ json_encode($coupons->map(fn($c) => [
            'id' => $c->id,
            'code' => $c->code,
            'name' => $c->name,
            'type' => $c->type,
            'value' => (float)$c->value,
            'min_order_amount' => (float)$c->min_order_amount,
            'max_discount' => $c->max_discount ? (float)$c->max_discount : null,
            'usage_limit' => $c->usage_limit ? (int)$c->usage_limit : null,
            'used_count' => (int)$c->used_count,
            'expires_at' => $c->expires_at ? $c->expires_at->format('Y-m-d') : null,
            'expires_display' => $c->expires_at ? $c->expires_at->format('d/m/Y') : 'Vô thời hạn',
            'is_active' => (bool)$c->is_active,
            'is_expired' => $c->expires_at ? $c->expires_at->isPast() : false,
            'is_expiring_soon' => $c->expires_at ? ($c->expires_at->diffInDays(now()) <= 7 && $c->expires_at->isFuture()) : false,
            'is_out_of_usage' => $c->usage_limit ? ($c->used_count >= $c->usage_limit) : false,
            'is_almost_out_usage' => $c->usage_limit ? (($c->used_count / $c->usage_limit) >= 0.8 && $c->used_count < $c->usage_limit) : false,
            'is_loading' => false
        ])) }},

        showToast(msg) {
            this.toastMessage = msg;
            setTimeout(() => { this.toastMessage = ''; }, 3500);
        },

        formatMoney(val) {
            if (val === null || val === undefined || isNaN(val) || val === '') return '0 ₫';
            return new Intl.NumberFormat('vi-VN').format(val) + ' ₫';
        },

        copyCouponCode(code) {
            if (!code) return;
            const successMsg = `Đã sao chép mã '${code}'!`;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code).then(() => {
                    this.showToast(successMsg);
                }).catch(() => {
                    this.fallbackCopy(code, successMsg);
                });
            } else {
                this.fallbackCopy(code, successMsg);
            }
        },

        fallbackCopy(text, successMsg) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.top = '-9999px';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                this.showToast(successMsg);
            } catch (err) {
                prompt('Sao chép mã voucher:', text);
            }
            document.body.removeChild(textArea);
        },

        openCreateDrawer() {
            this.isEditing = false;
            this.formId = null;
            this.formCode = '';
            this.formName = '';
            this.formType = 'fixed';
            this.formValue = '';
            this.formMinOrder = '';
            this.formMaxDiscount = '';
            this.formUsageLimit = '';
            this.formExpiresAt = '';
            this.formIsActive = true;
            this.showDrawer = true;
        },

        openEditDrawer(c) {
            this.isEditing = true;
            this.formId = c.id;
            this.formCode = c.code;
            this.formName = c.name;
            this.formType = c.type;
            this.formValue = c.value;
            this.formMinOrder = c.min_order_amount > 0 ? c.min_order_amount : '';
            this.formMaxDiscount = c.max_discount || '';
            this.formUsageLimit = c.usage_limit || '';
            this.formExpiresAt = c.expires_at || '';
            this.formIsActive = c.is_active;
            this.activeMenuId = null;
            this.showDrawer = true;
        },

        getStatusBadge(c) {
            if (!c.is_active) {
                return { label: 'Tạm dừng', class: 'bg-gray-100 text-gray-500 border-gray-200' };
            }
            if (c.is_expired) {
                return { label: 'Hết hạn', class: 'bg-rose-50 text-rose-600 border-rose-200' };
            }
            if (c.is_out_of_usage) {
                return { label: 'Hết lượt', class: 'bg-rose-50 text-rose-600 border-rose-200' };
            }
            if (c.is_almost_out_usage) {
                return { label: 'Sắp hết lượt', class: 'bg-amber-50 text-amber-800 border-amber-200' };
            }
            if (c.is_expiring_soon) {
                return { label: 'Sắp hết hạn', class: 'bg-amber-50 text-amber-800 border-amber-200' };
            }
            return { label: 'Đang áp dụng', class: 'bg-emerald-50 text-emerald-800 border-emerald-200' };
        },

        async toggleStatus(c) {
            this.activeMenuId = null;
            c.is_loading = true;
            try {
                const res = await fetch(`/admin/coupons/${c.id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    c.is_active = data.is_active;
                    this.showToast(`Mã '${c.code}' -> ${data.status_label}`);
                }
            } catch (e) {
                alert('Không thể đổi trạng thái voucher.');
            } finally {
                c.is_loading = false;
            }
        },

        async deleteCoupon(c) {
            this.activeMenuId = null;
            const confirmMsg = c.used_count > 0 
                ? `Mã '${c.code}' đã có ${c.used_count} lượt sử dụng. Bạn có chắc chắn muốn xoá vĩnh viễn?`
                : `Xoá mã giảm giá '${c.code}' khỏi hệ thống?`;

            if (!confirm(confirmMsg)) return;

            try {
                const res = await fetch(`/admin/coupons/${c.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.coupons = this.coupons.filter(item => item.id !== c.id);
                    this.showToast(`Đã xoá mã '${c.code}'!`);
                }
            } catch (e) {
                alert('Không thể xoá mã giảm giá.');
            }
        },

        filteredCoupons() {
            return this.coupons.filter(c => {
                // Search query
                const q = this.searchQuery.toLowerCase();
                const matchesSearch = !this.searchQuery || c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q);

                // Type filter
                const matchesType = this.typeFilter === 'all' || c.type === this.typeFilter;

                // Status filter
                let matchesStatus = true;
                if (this.statusFilter === 'active') {
                    matchesStatus = c.is_active && !c.is_expired && !c.is_out_of_usage;
                } else if (this.statusFilter === 'inactive') {
                    matchesStatus = !c.is_active;
                } else if (this.statusFilter === 'expired') {
                    matchesStatus = c.is_expired;
                } else if (this.statusFilter === 'out_of_usage') {
                    matchesStatus = c.is_out_of_usage;
                }

                return matchesSearch && matchesType && matchesStatus;
            });
        }
    }"
    @click="activeMenuId = null"
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
        class="fixed bottom-20 md:bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-2.5 rounded-2xl shadow-2xl border border-gray-700 flex items-center gap-2.5 text-xs font-bold"
        x-cloak
    >
        <span class="text-sm">🎟️</span>
        <span x-text="toastMessage"></span>
    </div>

    <!-- 1. HEADER (TITLE + 1-CLICK TẠO VOUCHER) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>🎟️ Quản Lý Voucher & Mã Giảm Giá</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">
                Tạo mã ưu đãi tiền mặt hoặc phần trăm áp dụng trực tiếp trong giỏ hàng.
            </p>
        </div>

        <button 
            type="button" 
            @click="openCreateDrawer()"
            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-sm hover:scale-[1.01] active:scale-95 transition-all cursor-pointer flex items-center gap-1.5 self-start sm:self-center"
        >
            <span>➕</span>
            <span>Tạo voucher mới</span>
        </button>
    </div>

    <!-- 2. SUMMARY 4 STAT CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Đang hoạt động</span>
                <span class="text-xl font-black text-emerald-600 font-mono">{{ $activeCount }}</span>
            </div>
            <span class="text-xl">🟢</span>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Tổng số mã</span>
                <span class="text-xl font-black text-gray-900 font-mono">{{ $totalCount }}</span>
            </div>
            <span class="text-xl">🎟️</span>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Lượt đã sử dụng</span>
                <span class="text-xl font-black text-blue-600 font-mono">{{ $totalUsedCount }}</span>
            </div>
            <span class="text-xl">📈</span>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Hạn sử dụng</span>
                <span class="text-xl font-black {{ $expiringCount > 0 ? 'text-amber-600' : 'text-gray-400' }} font-mono">{{ $expiringCount }}</span>
            </div>
            <span class="text-xl">⚠️</span>
        </div>
    </div>

    <!-- 3. TOOLBAR TÌM KIẾM & BỘ LỌC -->
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        
        <!-- Search Box -->
        <div class="relative flex-1 max-w-sm">
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="🔍 Tìm theo mã code hoặc tên voucher..." 
                class="w-full pl-3 pr-8 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all"
            >
            <button 
                type="button" 
                x-show="searchQuery" 
                @click="searchQuery = ''" 
                class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 font-bold cursor-pointer"
                x-cloak
            >
                ✕
            </button>
        </div>

        <!-- Filter Dropdowns -->
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Trạng thái -->
            <select 
                x-model="statusFilter"
                class="px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-700 outline-none cursor-pointer"
            >
                <option value="all">Tất cả trạng thái</option>
                <option value="active">🟢 Đang hoạt động</option>
                <option value="inactive">⚫ Đã tắt</option>
                <option value="expired">⚪ Đã hết hạn</option>
                <option value="out_of_usage">🔴 Đã hết lượt</option>
            </select>

            <!-- Loại giảm -->
            <select 
                x-model="typeFilter"
                class="px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-700 outline-none cursor-pointer"
            >
                <option value="all">Tất cả loại giảm</option>
                <option value="fixed">💵 Tiền mặt (VNĐ)</option>
                <option value="percent">% Phần trăm</option>
            </select>
        </div>

    </div>

    <!-- 4. BẢNG DANH SÁCH VOUCHER (COMPACT, FULL-WIDTH) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-2">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3.5">Mã Voucher</th>
                        <th class="px-4 py-3.5">Mức Giảm</th>
                        <th class="px-4 py-3.5">Đơn Tối Thiểu</th>
                        <th class="px-4 py-3.5">Đã Dùng</th>
                        <th class="px-4 py-3.5">Hạn Sử Dụng</th>
                        <th class="px-4 py-3.5 text-center w-36">Trạng Thái</th>
                        <th class="px-4 py-3.5 text-right w-20">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    <template x-for="c in filteredCoupons()" :key="c.id">
                        <tr class="hover:bg-gray-50/60 transition-colors" :class="!c.is_active || c.is_expired || c.is_out_of_usage ? 'opacity-70 bg-gray-50/30' : ''">
                            
                            <!-- 1. Mã Code (Click để copy) + Tên chương trình -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="space-y-0.5">
                                    <button 
                                        type="button" 
                                        @click="copyCouponCode(c.code)"
                                        class="inline-flex items-center gap-1 font-mono font-black text-xs text-red-600 bg-red-50 hover:bg-red-100 border border-red-200/80 px-2 py-0.5 rounded-lg transition-colors cursor-pointer group"
                                        title="Click để sao chép mã"
                                    >
                                        <span x-text="c.code"></span>
                                        <span class="text-[10px] opacity-40 group-hover:opacity-100">📋</span>
                                    </button>
                                    <span class="block text-gray-700 text-xs font-semibold max-w-[220px] truncate" :title="c.name" x-text="c.name"></span>
                                </div>
                            </td>

                            <!-- 2. Mức Giảm -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div>
                                    <template x-if="c.type === 'fixed'">
                                        <span class="font-black text-emerald-700 text-xs font-mono" x-text="'-' + formatMoney(c.value)"></span>
                                    </template>
                                    <template x-if="c.type === 'percent'">
                                        <div>
                                            <span class="font-black text-emerald-700 text-xs font-mono" x-text="'-' + c.value + '%'"></span>
                                            <span class="text-[10px] text-gray-400 block" x-show="c.max_discount" x-text="'Tối đa ' + formatMoney(c.max_discount)"></span>
                                        </div>
                                    </template>
                                </div>
                            </td>

                            <!-- 3. Đơn Tối Thiểu -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-gray-900" x-text="c.min_order_amount > 0 ? formatMoney(c.min_order_amount) : '0 ₫'"></span>
                            </td>

                            <!-- 4. Đã Dùng / Giới Hạn -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold font-mono text-gray-800" x-text="c.used_count + ' / ' + (c.usage_limit ? c.usage_limit : '∞')"></span>
                            </td>

                            <!-- 5. Hạn Sử Dụng -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-gray-700" :class="c.is_expired ? 'text-gray-400 line-through' : (c.is_expiring_soon ? 'text-amber-700 font-bold' : '')" x-text="c.expires_display"></span>
                            </td>

                            <!-- 6. Trạng Thái (1-CLICK TOGGLE BUTTON TRỰC TIẾP) -->
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <button 
                                    type="button" 
                                    @click="toggleStatus(c)"
                                    :disabled="c.is_loading"
                                    title="Click để Bật / Tạm dừng mã voucher này"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer shadow-2xs border"
                                    :class="c.is_active 
                                        ? (c.is_expired || c.is_out_of_usage ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border-emerald-200') 
                                        : 'bg-gray-100 hover:bg-gray-200 text-gray-600 border-gray-200'"
                                >
                                    <span class="w-2 h-2 rounded-full" :class="c.is_active ? (c.is_expired || c.is_out_of_usage ? 'bg-rose-500' : 'bg-emerald-600 animate-pulse') : 'bg-gray-400'"></span>
                                    <span x-text="getStatusBadge(c).label"></span>
                                </button>
                            </td>

                            <!-- 7. Thao Tác (Sửa + Menu ⋮) -->
                            <td class="px-4 py-3 whitespace-nowrap text-right relative" @click.stop>
                                <div class="inline-flex items-center justify-end gap-1">
                                    
                                    <!-- Nút Sửa -->
                                    <button 
                                        type="button" 
                                        @click="openEditDrawer(c)"
                                        class="p-1.5 rounded-lg bg-gray-50 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors cursor-pointer"
                                        title="Chỉnh sửa voucher"
                                    >
                                        ✏️
                                    </button>

                                    <!-- Menu 3 chấm -->
                                    <div class="relative">
                                        <button 
                                            type="button" 
                                            @click="activeMenuId = (activeMenuId === c.id ? null : c.id)"
                                            class="w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-600 font-black text-sm flex items-center justify-center transition-colors cursor-pointer"
                                        >
                                            ⋮
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div 
                                            x-show="activeMenuId === c.id" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl shadow-xl border border-gray-200 py-1.5 text-xs text-left z-30 font-medium"
                                            x-cloak
                                        >
                                            <button 
                                                type="button" 
                                                @click="copyCouponCode(c.code); activeMenuId = null;"
                                                class="w-full px-3 py-1.5 hover:bg-gray-50 flex items-center gap-2 text-gray-700 text-left cursor-pointer"
                                            >
                                                <span>📋</span>
                                                <span>Sao chép mã</span>
                                            </button>

                                            <button 
                                                type="button" 
                                                @click="openEditDrawer(c)"
                                                class="w-full px-3 py-1.5 hover:bg-gray-50 flex items-center gap-2 text-gray-700 text-left cursor-pointer"
                                            >
                                                <span>✏️</span>
                                                <span>Chỉnh sửa</span>
                                            </button>

                                            <button 
                                                type="button" 
                                                @click="toggleStatus(c)"
                                                class="w-full px-3 py-1.5 hover:bg-gray-50 flex items-center gap-2 text-gray-700 text-left cursor-pointer"
                                            >
                                                <span>🔄</span>
                                                <span x-text="c.is_active ? 'Tạm dừng mã' : 'Bật mở lại mã'"></span>
                                            </button>

                                            <div class="my-1 border-t border-gray-100"></div>

                                            <button 
                                                type="button" 
                                                @click="deleteCoupon(c)"
                                                class="w-full px-3 py-1.5 hover:bg-rose-50 text-rose-600 flex items-center gap-2 text-left cursor-pointer font-bold"
                                            >
                                                <span>🗑️</span>
                                                <span>Xóa voucher</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    </template>

                    <template x-if="filteredCoupons().length === 0">
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-400 text-xs">
                                Không tìm thấy mã giảm giá nào phù hợp.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. SLIDE-OVER DRAWER TẠO / SỬA VOUCHER (520PX) -->
    <div 
        x-show="showDrawer" 
        class="fixed inset-0 z-50 overflow-hidden" 
        x-cloak
    >
        <div class="absolute inset-0 overflow-hidden">
            
            <!-- Backdrop -->
            <div 
                x-show="showDrawer"
                x-transition:enter="ease-in-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-black/50 backdrop-blur-xs transition-opacity" 
                @click="showDrawer = false"
            ></div>

            <!-- Drawer Panel -->
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div 
                    x-show="showDrawer"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen max-w-lg bg-white shadow-2xl flex flex-col justify-between"
                >
                    
                    <!-- Drawer Header -->
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-black text-base text-gray-900" x-text="isEditing ? '✏️ Chỉnh Sửa Mã Giảm Giá' : '➕ Tạo Mã Giảm Giá Mới'"></h3>
                            <p class="text-xs text-gray-400" x-text="isEditing ? 'Cập nhật điều kiện & hạn dùng voucher' : 'Áp dụng trực tiếp trong giỏ hàng & thanh toán'"></p>
                        </div>
                        <button 
                            @click="showDrawer = false" 
                            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold flex items-center justify-center transition-colors cursor-pointer"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Drawer Form Body (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-5 space-y-4 text-xs">
                        
                        <form 
                            id="voucherDrawerForm"
                            :action="isEditing ? '/admin/coupons/' + formId : '{{ route('admin.coupons.store') }}'" 
                            method="POST" 
                            class="space-y-4"
                        >
                            @csrf
                            <template x-if="isEditing">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <!-- SECTION 1: THÔNG TIN VOUCHER -->
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 space-y-3">
                                <span class="font-black text-[11px] text-gray-400 uppercase tracking-wider block">1. Thông tin voucher</span>

                                <!-- Mã Code -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700">Mã Code Voucher <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        name="code" 
                                        x-model="formCode"
                                        @input="formCode = formCode.toUpperCase().replace(/\s+/g, '')"
                                        placeholder="VD: FREESHIP15, CHICKEN20..." 
                                        required
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-black text-red-600 uppercase tracking-wider focus:border-red-500 outline-none font-mono"
                                    >
                                </div>

                                <!-- Tên Chương Trình -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700">Tên Chương Trình <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        x-model="formName"
                                        placeholder="VD: Giảm 20k cho đơn từ 120k..." 
                                        required
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-900 focus:border-red-500 outline-none"
                                    >
                                </div>
                            </div>

                            <!-- SECTION 2: MỨC GIẢM -->
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 space-y-3">
                                <span class="font-black text-[11px] text-gray-400 uppercase tracking-wider block">2. Mức giảm giá</span>

                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Loại giảm -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-gray-700">Loại Giảm <span class="text-red-500">*</span></label>
                                        <select 
                                            name="type" 
                                            x-model="formType"
                                            class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-900 outline-none cursor-pointer"
                                        >
                                            <option value="fixed">Tiền mặt (VNĐ)</option>
                                            <option value="percent">Phần trăm (%)</option>
                                        </select>
                                    </div>

                                    <!-- Giá trị giảm -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-gray-700">Mức Giảm <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <input 
                                                type="number" 
                                                name="value" 
                                                x-model="formValue"
                                                placeholder="20000 hoặc 10" 
                                                required
                                                min="1"
                                                :max="formType === 'percent' ? 100 : null"
                                                class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-black text-gray-900 outline-none text-right pr-7 font-mono"
                                            >
                                            <span class="absolute right-2.5 top-2 text-xs font-bold text-gray-400" x-text="formType === 'fixed' ? 'đ' : '%'">đ</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Giảm tối đa nếu là % -->
                                <div class="space-y-1" x-show="formType === 'percent'" x-cloak>
                                    <label class="block text-[11px] font-bold text-gray-700">Giảm Tối Đa (VNĐ)</label>
                                    <input 
                                        type="number" 
                                        name="max_discount" 
                                        x-model="formMaxDiscount"
                                        placeholder="50000" 
                                        step="1000"
                                        class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-900 outline-none text-right font-mono"
                                    >
                                </div>
                            </div>

                            <!-- SECTION 3: ĐIỀU KIỆN ÁP DỤNG -->
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 space-y-3">
                                <span class="font-black text-[11px] text-gray-400 uppercase tracking-wider block">3. Điều kiện áp dụng</span>

                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Đơn tối thiểu -->
                                    <div class="space-y-1">
                                        <label class="block text-[11px] font-bold text-gray-700">Đơn Tối Thiểu (VNĐ)</label>
                                        <input 
                                            type="number" 
                                            name="min_order_amount" 
                                            x-model="formMinOrder"
                                            placeholder="0" 
                                            step="1000"
                                            min="0"
                                            class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-900 outline-none text-right font-mono"
                                        >
                                    </div>

                                    <!-- Giới hạn lượt dùng -->
                                    <div class="space-y-1">
                                        <label class="block text-[11px] font-bold text-gray-700">Lượt Dùng Tối Đa</label>
                                        <input 
                                            type="number" 
                                            name="usage_limit" 
                                            x-model="formUsageLimit"
                                            placeholder="Không giới hạn" 
                                            min="1"
                                            class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-900 outline-none text-right font-mono"
                                        >
                                    </div>
                                </div>

                                <!-- Hạn sử dụng -->
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-bold text-gray-700">Hạn Sử Dụng</label>
                                    <input 
                                        type="date" 
                                        name="expires_at" 
                                        x-model="formExpiresAt"
                                        min="{{ date('Y-m-d') }}"
                                        class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-900 outline-none font-mono"
                                    >
                                </div>
                            </div>

                            <!-- SECTION 4: LIVE PREVIEW THẺ VOUCHER -->
                            <div class="p-3.5 bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl border border-red-200/70 space-y-1.5">
                                <span class="text-[10px] font-black text-red-700 uppercase tracking-wider block">👁️ Xem trước hiển thị trên web:</span>
                                
                                <div class="bg-white p-3 rounded-xl border border-red-200 shadow-xs flex items-center justify-between">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono font-black text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-md" x-text="formCode || 'MÃ_VOUCHER'"></span>
                                            <span class="font-bold text-xs text-gray-900" x-text="formName || 'Tên chương trình ưu đãi'"></span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 font-medium">
                                            <span x-text="formType === 'fixed' ? 'Giảm ' + formatMoney(formValue) : 'Giảm ' + (formValue || '0') + '%'"></span>
                                            <span> · Đơn từ </span>
                                            <span x-text="formatMoney(formMinOrder || 0)"></span>
                                            <span x-show="formExpiresAt" x-text="' · Hạn: ' + formExpiresAt"></span>
                                        </p>
                                    </div>
                                    <span class="text-xl">🎟️</span>
                                </div>
                            </div>

                        </form>

                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between gap-3 bg-gray-50">
                        <button 
                            type="button" 
                            @click="showDrawer = false" 
                            class="px-4 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs transition-colors cursor-pointer"
                        >
                            Hủy
                        </button>

                        <button 
                            type="button" 
                            @click="document.getElementById('voucherDrawerForm').submit()"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.01] active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                        >
                            <span>🎟️</span>
                            <span x-text="isEditing ? 'Lưu cập nhật voucher' : 'Tạo mã voucher'"></span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
