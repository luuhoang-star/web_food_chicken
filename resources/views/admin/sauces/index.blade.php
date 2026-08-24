@extends('layouts.admin')

@section('title', 'Quản Lý Vị Sốt & Topping')
@section('page_title', '🌶️ Quản Lý Vị Sốt & Topping')

@section('content')
<div 
    class="max-w-5xl space-y-6 pb-20" 
    x-data="{
        searchQuery: '',
        statusFilter: 'all',
        toastMessage: '',
        isDirty: false,
        editingToppingId: null,
        tempToppingPrice: '',

        sauces: {{ json_encode($sauces->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'slug' => $s->slug,
            'tagline' => $s->tagline,
            'price' => (int)$s->price,
            'description' => $s->description,
            'color' => $s->slug === 'sot-cay-han' ? 'bg-red-600 ring-red-200' : ($s->slug === 'sot-pho-mai' ? 'bg-amber-400 ring-amber-200' : ($s->slug === 'sot-toi-tay' ? 'bg-lime-500 ring-lime-200' : 'bg-orange-600 ring-orange-200')),
            'is_dirty' => false,
            'saved_price' => (int)$s->price,
            'saved_tagline' => $s->tagline
        ])) }},

        toppings: {{ json_encode($toppings->map(fn($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'price' => (int)$t->price,
            'is_available' => (bool)$t->is_available,
            'is_loading' => false
        ])) }},

        showToast(msg) {
            this.toastMessage = msg;
            setTimeout(() => { this.toastMessage = ''; }, 3500);
        },

        formatMoney(val) {
            if (val === null || val === undefined || isNaN(val)) return '0 ₫';
            return new Intl.NumberFormat('vi-VN').format(val) + ' ₫';
        },

        // VỊ SỐT ACTIONS
        onSauceChange(idx) {
            const s = this.sauces[idx];
            s.is_dirty = (s.price != s.saved_price || s.tagline != s.saved_tagline);
            this.checkGlobalDirty();
        },

        checkGlobalDirty() {
            this.isDirty = this.sauces.some(s => s.is_dirty);
        },

        async saveSauce(idx) {
            const s = this.sauces[idx];
            try {
                const res = await fetch(`/admin/sauces/${s.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tagline: s.tagline,
                        price: s.price,
                        description: s.description
                    })
                });
                const data = await res.json();
                if (data.success) {
                    s.saved_price = s.price;
                    s.saved_tagline = s.tagline;
                    s.is_dirty = false;
                    this.checkGlobalDirty();
                    this.showToast(`Đã lưu vị sốt '${s.name}'!`);
                }
            } catch (e) {
                alert('Có lỗi khi lưu vị sốt. Vui lòng thử lại.');
            }
        },

        async saveAllSauces() {
            const dirtySauces = this.sauces.filter(s => s.is_dirty);
            for (const s of dirtySauces) {
                const idx = this.sauces.findIndex(item => item.id === s.id);
                if (idx !== -1) {
                    await this.saveSauce(idx);
                }
            }
            this.showToast('Đã lưu tất cả thay đổi vị sốt!');
        },

        cancelSauceChanges() {
            this.sauces.forEach(s => {
                s.price = s.saved_price;
                s.tagline = s.saved_tagline;
                s.is_dirty = false;
            });
            this.isDirty = false;
        },

        // TOPPING ACTIONS
        startEditTopping(t) {
            this.editingToppingId = t.id;
            this.tempToppingPrice = t.price;
            this.$nextTick(() => {
                const el = document.getElementById('topping-price-input-' + t.id);
                if (el) { el.focus(); el.select(); }
            });
        },

        cancelEditTopping() {
            this.editingToppingId = null;
            this.tempToppingPrice = '';
        },

        async saveToppingPrice(t) {
            if (this.tempToppingPrice === '' || isNaN(this.tempToppingPrice) || this.tempToppingPrice < 0) {
                this.cancelEditTopping();
                return;
            }
            const oldPrice = t.price;
            t.price = parseInt(this.tempToppingPrice);
            this.editingToppingId = null;

            try {
                const res = await fetch(`/admin/toppings/${t.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ price: t.price })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast(`Đã cập nhật giá '${t.name}': ${this.formatMoney(t.price)}`);
                } else {
                    t.price = oldPrice;
                    alert('Không thể lưu giá topping.');
                }
            } catch (e) {
                t.price = oldPrice;
                alert('Lỗi kết nối khi lưu giá topping.');
            }
        },

        async toggleToppingStatus(t) {
            t.is_loading = true;
            try {
                const res = await fetch(`/admin/toppings/${t.id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    t.is_available = data.is_available;
                    this.showToast(`'${t.name}' -> ${data.status_label}`);
                }
            } catch (e) {
                alert('Không thể đổi trạng thái topping.');
            } finally {
                t.is_loading = false;
            }
        },

        // FILTER COMPUTED
        filteredSauces() {
            if (!this.searchQuery) return this.sauces;
            const q = this.searchQuery.toLowerCase();
            return this.sauces.filter(s => s.name.toLowerCase().includes(q) || (s.tagline && s.tagline.toLowerCase().includes(q)));
        },

        filteredToppings() {
            return this.toppings.filter(t => {
                const matchesSearch = !this.searchQuery || t.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchesStatus = this.statusFilter === 'all' 
                    || (this.statusFilter === 'available' && t.is_available)
                    || (this.statusFilter === 'out_of_stock' && !t.is_available);
                return matchesSearch && matchesStatus;
            });
        }
    }"
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
        <span class="text-sm">✓</span>
        <span x-text="toastMessage"></span>
    </div>

    <!-- 1. TOP TOOLBAR: SUMMARY & SEARCH / FILTER -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        
        <!-- Summary Stats Strip -->
        <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
            <span class="px-2.5 py-1 rounded-xl bg-gray-100 text-gray-900 font-mono">
                🌶️ <span x-text="sauces.length"></span> vị sốt · 🍳 <span x-text="toppings.length"></span> topping · <span x-text="sauces.length + toppings.length"></span> mục
            </span>
        </div>

        <!-- Search & Status Dropdown -->
        <div class="flex items-center gap-2 flex-1 sm:justify-end">
            <!-- Search -->
            <div class="relative flex-1 max-w-xs">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="🔍 Tìm sốt hoặc topping..." 
                    class="w-full pl-3 pr-8 py-1.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all"
                >
                <button 
                    type="button" 
                    x-show="searchQuery" 
                    @click="searchQuery = ''" 
                    class="absolute right-2.5 top-2 text-gray-400 hover:text-gray-600 text-xs font-bold"
                    x-cloak
                >
                    ✕
                </button>
            </div>

            <!-- Status filter for Toppings -->
            <select 
                x-model="statusFilter"
                class="px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 outline-none cursor-pointer"
            >
                <option value="all">Tất cả trạng thái</option>
                <option value="available">🟢 Đang phục vụ</option>
                <option value="out_of_stock">⚪ Hết hàng</option>
            </select>
        </div>

    </div>

    <!-- 2. KHU VỰC 1: 🌶️ VỊ SỐT ĐẶC TRƯNG -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-2">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🌶️</span>
                    <span>Vị Sốt Đặc Trưng</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-xs font-bold" x-text="filteredSauces().length + ' loại'"></span>
            </div>
            <span class="text-[11px] text-gray-400 font-medium">Giá hũ nhỏ mua thêm & slogan nhận diện</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">Tên Vị Sốt</th>
                        <th class="px-4 py-3">Khẩu Hiệu Nhận Diện (1 dòng)</th>
                        <th class="px-4 py-3 text-right">Giá Hũ Mua Thêm</th>
                        <th class="px-4 py-3 text-right w-24">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    <template x-for="(s, idx) in filteredSauces()" :key="s.id">
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            
                            <!-- Tên sốt -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-3 h-3 rounded-full ring-2 shrink-0" :class="s.color"></span>
                                    <span class="font-black text-gray-900 text-xs" x-text="s.name"></span>
                                </div>
                            </td>

                            <!-- Khẩu hiệu nhận diện -->
                            <td class="px-4 py-3 max-w-sm">
                                <input 
                                    type="text" 
                                    x-model="s.tagline" 
                                    @input="onSauceChange(idx)"
                                    @blur="if(s.is_dirty) saveSauce(idx)"
                                    placeholder="Khẩu hiệu nhận diện sốt..." 
                                    class="w-full px-2.5 py-1.5 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-red-500 text-xs font-semibold text-gray-800 outline-none transition-all truncate"
                                >
                            </td>

                            <!-- Giá hũ bán thêm -->
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex items-center justify-end gap-1">
                                    <input 
                                        type="number" 
                                        x-model="s.price" 
                                        @input="onSauceChange(idx)"
                                        @blur="if(s.is_dirty) saveSauce(idx)"
                                        step="1000" 
                                        min="0"
                                        class="w-24 px-2.5 py-1.5 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-red-500 text-xs font-black text-red-600 outline-none text-right transition-all font-mono"
                                    >
                                    <span class="text-xs text-gray-400 font-bold">đ</span>
                                </div>
                            </td>

                            <!-- Trạng thái Dirty -->
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span x-show="s.is_dirty" class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse" x-cloak>
                                    Chưa lưu
                                </span>
                                <span x-show="!s.is_dirty" class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                    Đã lưu
                                </span>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. KHU VỰC 2: 🍳 TOPPING ĂN KÈM -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-2">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🍳</span>
                    <span>Topping Ăn Kèm & Giá Bán Thêm</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 text-xs font-bold" x-text="filteredToppings().length + ' loại'"></span>
            </div>
            <span class="text-[11px] text-gray-400 font-medium">Click vào giá để sửa trực tiếp (Enter để lưu)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">Tên Topping</th>
                        <th class="px-4 py-3 text-right">Giá Bán Thêm</th>
                        <th class="px-4 py-3 text-center w-36">Trạng Thái Phục Vụ</th>
                        <th class="px-4 py-3 text-right w-28">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    <template x-for="t in filteredToppings()" :key="t.id">
                        <tr class="hover:bg-gray-50/50 transition-colors" :class="!t.is_available ? 'opacity-60 bg-gray-50/40' : ''">
                            
                            <!-- Tên topping -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-black text-gray-900 text-xs" x-text="t.name"></span>
                            </td>

                            <!-- Giá bán thêm (Inline Edit 1-chạm) -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <!-- Mode xem: Click để sửa -->
                                <div 
                                    x-show="editingToppingId !== t.id" 
                                    @click="startEditTopping(t)"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-gray-100 cursor-pointer group transition-colors"
                                    title="Click để chỉnh giá nhanh"
                                >
                                    <span class="font-black font-mono text-xs text-gray-900 group-hover:text-red-600" x-text="formatMoney(t.price)"></span>
                                    <span class="text-[10px] text-gray-300 group-hover:text-gray-500">✏️</span>
                                </div>

                                <!-- Mode sửa inline: Enter lưu, Esc huỷ -->
                                <div x-show="editingToppingId === t.id" class="inline-flex items-center gap-1" x-cloak>
                                    <input 
                                        :id="'topping-price-input-' + t.id"
                                        type="number" 
                                        x-model="tempToppingPrice"
                                        step="1000"
                                        min="0"
                                        @keydown.enter.prevent="saveToppingPrice(t)"
                                        @keydown.escape.prevent="cancelEditTopping()"
                                        @blur="saveToppingPrice(t)"
                                        class="w-24 px-2 py-1 rounded-lg bg-white border-2 border-red-500 text-xs font-black text-red-600 text-right outline-none font-mono shadow-xs"
                                    >
                                    <span class="text-xs text-gray-400 font-bold">đ</span>
                                </div>
                            </td>

                            <!-- Trạng thái Toggle 1-Click -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <button 
                                    type="button" 
                                    @click="toggleToppingStatus(t)"
                                    :disabled="t.is_loading"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer shadow-2xs"
                                    :class="t.is_available ? 'bg-emerald-100 hover:bg-emerald-200 text-emerald-800 border border-emerald-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-500 border border-gray-200'"
                                >
                                    <span class="w-2 h-2 rounded-full" :class="t.is_available ? 'bg-emerald-600 animate-pulse' : 'bg-gray-400'"></span>
                                    <span x-text="t.is_available ? '🟢 Đang phục vụ' : '⚪ Hết hàng'"></span>
                                </button>
                            </td>

                            <!-- Thao tác nhanh -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <button 
                                    type="button" 
                                    @click="startEditTopping(t)"
                                    class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-[11px] transition-colors cursor-pointer"
                                >
                                    Sửa giá
                                </button>
                            </td>

                        </tr>
                    </template>

                    <template x-if="filteredToppings().length === 0">
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-400 text-xs">
                                Không tìm thấy topping nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. STICKY BULK ACTION BAR (KHI CÓ THAY ĐỔI VỊ SỐT CHƯA LƯU) -->
    <div 
        x-show="isDirty" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-2xl py-3 px-4 sm:px-8"
        x-cloak
    >
        <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200/70 px-2.5 py-1 rounded-xl animate-pulse">
                    <span>●</span>
                    <span x-text="sauces.filter(s => s.is_dirty).length + ' vị sốt có thay đổi chưa lưu'"></span>
                </span>
            </div>

            <div class="flex items-center gap-2.5">
                <button 
                    type="button" 
                    @click="cancelSauceChanges()"
                    class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors cursor-pointer"
                >
                    Hủy
                </button>

                <button 
                    type="button" 
                    @click="saveAllSauces()"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.01] active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                >
                    <span>💾</span>
                    <span>Lưu thay đổi</span>
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
