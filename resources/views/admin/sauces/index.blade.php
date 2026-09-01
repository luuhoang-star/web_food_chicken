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

        showAddSauceModal: false,
        newSauceName: '',
        newSauceTagline: '',
        newSaucePrice: 10000,
        isAddingSauce: false,

        showAddToppingModal: false,
        newToppingName: '',
        newToppingPrice: 10000,
        isAddingTopping: false,

        sauces: {{ json_encode($sauces->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'slug' => $s->slug,
            'tagline' => $s->tagline,
            'price' => (int)$s->price,
            'description' => $s->description,
            'color' => $s->slug === 'sot-cay-han' ? 'bg-red-600 ring-red-200' : ($s->slug === 'sot-pho-mai' ? 'bg-amber-400 ring-amber-200' : ($s->slug === 'sot-toi-tay' ? 'bg-lime-500 ring-lime-200' : 'bg-orange-600 ring-orange-200')),
            'is_dirty' => false,
            'saved_name' => $s->name,
            'saved_price' => (int)$s->price,
            'saved_tagline' => $s->tagline
        ])) }},

        toppings: {{ json_encode($toppings->map(fn($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'price' => (int)$t->price,
            'is_available' => (bool)$t->is_available,
            'saved_name' => $t->name,
            'saved_price' => (int)$t->price,
            'is_dirty' => false,
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
            s.is_dirty = (s.name !== s.saved_name || s.price != s.saved_price || s.tagline !== s.saved_tagline);
            this.checkGlobalDirty();
        },

        checkGlobalDirty() {
            this.isDirty = this.sauces.some(s => s.is_dirty);
        },

        async saveSauce(idx) {
            const s = this.sauces[idx];
            if (!s.name || !s.name.trim()) {
                s.name = s.saved_name;
                return;
            }
            try {
                const res = await fetch(`/admin/sauces/${s.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: s.name,
                        tagline: s.tagline,
                        price: s.price,
                        description: s.description
                    })
                });
                const data = await res.json();
                if (data.success) {
                    s.saved_name = s.name;
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
                s.name = s.saved_name;
                s.price = s.saved_price;
                s.tagline = s.saved_tagline;
                s.is_dirty = false;
            });
            this.isDirty = false;
        },

        async deleteSauce(s, idx) {
            if (!confirm(`Bạn có chắc chắn muốn xoá vị sốt '${s.name}' khỏi hệ thống?`)) return;
            try {
                const res = await fetch(`/admin/sauces/${s.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.sauces.splice(idx, 1);
                    this.checkGlobalDirty();
                    this.showToast(`Đã xoá vị sốt '${s.name}' thành công!`);
                } else {
                    alert('Không thể xoá vị sốt.');
                }
            } catch (e) {
                alert('Lỗi kết nối khi xoá vị sốt.');
            }
        },

        async createSauce() {
            if (!this.newSauceName.trim()) {
                alert('Vui lòng nhập tên vị sốt.');
                return;
            }
            this.isAddingSauce = true;
            try {
                const res = await fetch('/admin/sauces', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.newSauceName,
                        tagline: this.newSauceTagline,
                        price: this.newSaucePrice
                    })
                });
                const data = await res.json();
                if (data.success && data.sauce) {
                    this.sauces.push(data.sauce);
                    this.newSauceName = '';
                    this.newSauceTagline = '';
                    this.newSaucePrice = 10000;
                    this.showAddSauceModal = false;
                    this.showToast(data.message);
                } else {
                    alert(data.message || 'Không thể tạo vị sốt mới.');
                }
            } catch (e) {
                alert('Lỗi kết nối khi tạo vị sốt.');
            } finally {
                this.isAddingSauce = false;
            }
        },

        // TOPPING ACTIONS
        onToppingChange(t) {
            t.is_dirty = (t.name !== t.saved_name || t.price != t.saved_price);
        },

        async saveTopping(t) {
            if (!t.name || !t.name.trim()) {
                t.name = t.saved_name;
                return;
            }
            if (t.price === '' || isNaN(t.price) || t.price < 0) {
                t.price = t.saved_price;
                return;
            }

            try {
                const res = await fetch(`/admin/toppings/${t.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        name: t.name,
                        price: t.price 
                    })
                });
                const data = await res.json();
                if (data.success) {
                    t.saved_name = t.name;
                    t.saved_price = t.price;
                    t.is_dirty = false;
                    this.showToast(`Đã lưu topping '${t.name}': ${this.formatMoney(t.price)}`);
                } else {
                    t.name = t.saved_name;
                    t.price = t.saved_price;
                    alert('Không thể lưu topping.');
                }
            } catch (e) {
                t.name = t.saved_name;
                t.price = t.saved_price;
                alert('Lỗi kết nối khi lưu topping.');
            }
        },

        async deleteTopping(t) {
            if (!confirm(`Bạn có chắc chắn muốn xoá topping '${t.name}' khỏi hệ thống?`)) return;
            try {
                const res = await fetch(`/admin/toppings/${t.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const idx = this.toppings.findIndex(item => item.id === t.id);
                    if (idx !== -1) {
                        this.toppings.splice(idx, 1);
                    }
                    this.showToast(`Đã xoá topping '${t.name}' thành công!`);
                } else {
                    alert('Không thể xoá topping.');
                }
            } catch (e) {
                alert('Lỗi kết nối khi xoá topping.');
            }
        },

        async createTopping() {
            if (!this.newToppingName.trim()) {
                alert('Vui lòng nhập tên topping.');
                return;
            }
            this.isAddingTopping = true;
            try {
                const res = await fetch('/admin/toppings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.newToppingName,
                        price: this.newToppingPrice
                    })
                });
                const data = await res.json();
                if (data.success && data.topping) {
                    this.toppings.push(data.topping);
                    this.newToppingName = '';
                    this.newToppingPrice = 10000;
                    this.showAddToppingModal = false;
                    this.showToast(data.message);
                } else {
                    alert(data.message || 'Không thể tạo topping mới.');
                }
            } catch (e) {
                alert('Lỗi kết nối khi tạo topping.');
            } finally {
                this.isAddingTopping = false;
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
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🌶️</span>
                    <span>Vị Sốt Đặc Trưng</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-xs font-bold" x-text="filteredSauces().length + ' loại'"></span>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-gray-400 font-medium hidden md:inline">Click vào Tên, Slogan, Giá để sửa</span>
                <button 
                    type="button" 
                    @click="showAddSauceModal = true"
                    class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer"
                >
                    <span>+</span>
                    <span>Thêm vị sốt</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">Tên Vị Sốt</th>
                        <th class="px-4 py-3">Khẩu Hiệu Nhận Diện (1 dòng)</th>
                        <th class="px-4 py-3 text-right">Giá Hũ Mua Thêm</th>
                        <th class="px-4 py-3 text-right w-24">Trạng Thái</th>
                        <th class="px-4 py-3 text-center w-16">Xoá</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    <template x-for="(s, idx) in filteredSauces()" :key="s.id">
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            
                            <!-- Tên sốt (Sửa trực tiếp) -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-3.5 h-3.5 rounded-full ring-2 shrink-0 shadow-xs" :class="s.color"></span>
                                    <input 
                                        type="text" 
                                        x-model="s.name" 
                                        @input="onSauceChange(idx)"
                                        @blur="if(s.is_dirty) saveSauce(idx)"
                                        @keydown.enter.prevent="if(s.is_dirty) saveSauce(idx)"
                                        title="Click để sửa tên vị sốt trực tiếp"
                                        class="px-2.5 py-1.5 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-red-500 text-xs font-black text-gray-900 outline-none transition-all cursor-text"
                                    >
                                </div>
                            </td>

                            <!-- Khẩu hiệu nhận diện -->
                            <td class="px-4 py-3 max-w-sm">
                                <input 
                                    type="text" 
                                    x-model="s.tagline" 
                                    @input="onSauceChange(idx)"
                                    @blur="if(s.is_dirty) saveSauce(idx)"
                                    @keydown.enter.prevent="if(s.is_dirty) saveSauce(idx)"
                                    placeholder="Khẩu hiệu nhận diện sốt..." 
                                    title="Click để sửa khẩu hiệu"
                                    class="w-full px-2.5 py-1.5 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-red-500 text-xs font-semibold text-gray-800 outline-none transition-all truncate cursor-text"
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
                                        @keydown.enter.prevent="if(s.is_dirty) saveSauce(idx)"
                                        step="1000" 
                                        min="0"
                                        title="Click để sửa giá hũ mua thêm"
                                        class="w-24 px-2.5 py-1.5 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-red-500 text-xs font-black text-red-600 outline-none text-right transition-all font-mono cursor-text"
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

                            <!-- Nút Xoá Vị Sốt -->
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <button 
                                    type="button" 
                                    @click="deleteSauce(s, idx)"
                                    title="Xoá vị sốt này"
                                    class="p-1.5 rounded-lg text-gray-300 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                >
                                    🗑️
                                </button>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. KHU VỰC 2: 🍳 TOPPING ĂN KÈM -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-2">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🍳</span>
                    <span>Topping Ăn Kèm & Giá Bán Thêm</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 text-xs font-bold" x-text="filteredToppings().length + ' loại'"></span>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-gray-400 font-medium hidden md:inline">Click trực tiếp vào Tên hoặc Giá để sửa</span>
                <button 
                    type="button" 
                    @click="showAddToppingModal = true"
                    class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer"
                >
                    <span>+</span>
                    <span>Thêm topping</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">Tên Topping</th>
                        <th class="px-4 py-3 text-right w-44">Giá Bán Thêm</th>
                        <th class="px-4 py-3 text-center w-40">Trạng Thái Phục Vụ</th>
                        <th class="px-4 py-3 text-center w-16">Xoá</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    <template x-for="t in filteredToppings()" :key="t.id">
                        <tr class="hover:bg-gray-50/50 transition-colors group" :class="!t.is_available ? 'opacity-60 bg-gray-50/40' : ''">
                            
                            <!-- Tên topping (Sửa trực tiếp) -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input 
                                    type="text" 
                                    x-model="t.name" 
                                    @input="onToppingChange(t)"
                                    @blur="if(t.is_dirty) saveTopping(t)"
                                    @keydown.enter.prevent="if(t.is_dirty) saveTopping(t)"
                                    title="Click để sửa tên topping trực tiếp"
                                    class="w-full max-w-sm px-2.5 py-1.5 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-red-500 text-xs font-black text-gray-900 outline-none transition-all cursor-text"
                                >
                            </td>

                            <!-- Giá bán thêm (Sửa trực tiếp) -->
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex items-center justify-end gap-1">
                                    <input 
                                        type="number" 
                                        x-model="t.price" 
                                        @input="onToppingChange(t)"
                                        @blur="if(t.is_dirty) saveTopping(t)"
                                        @keydown.enter.prevent="if(t.is_dirty) saveTopping(t)"
                                        step="1000" 
                                        min="0"
                                        title="Click để sửa giá bán thêm"
                                        class="w-28 px-2.5 py-1.5 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-red-500 text-xs font-black text-red-600 outline-none text-right transition-all font-mono cursor-text"
                                    >
                                    <span class="text-xs text-gray-400 font-bold">đ</span>
                                </div>
                            </td>

                            <!-- Trạng thái Toggle 1-Click -->
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <button 
                                    type="button" 
                                    @click="toggleToppingStatus(t)"
                                    :disabled="t.is_loading"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer shadow-2xs"
                                    :class="t.is_available ? 'bg-emerald-100 hover:bg-emerald-200 text-emerald-800 border border-emerald-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-500 border border-gray-200'"
                                >
                                    <span class="w-2 h-2 rounded-full" :class="t.is_available ? 'bg-emerald-600 animate-pulse' : 'bg-gray-400'"></span>
                                    <span x-text="t.is_available ? '🟢 Đang phục vụ' : '⚪ Hết hàng'"></span>
                                </button>
                            </td>

                            <!-- Nút Xoá Topping -->
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <button 
                                    type="button" 
                                    @click="deleteTopping(t)"
                                    title="Xoá topping này"
                                    class="p-1.5 rounded-lg text-gray-300 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                >
                                    🗑️
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

    <!-- 4. MODAL THÊM VỊ SỐT MỚI -->
    <div 
        x-show="showAddSauceModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" 
        x-cloak
    >
        <div 
            @click.outside="showAddSauceModal = false"
            class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-4 border border-gray-100"
        >
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🌶️</span>
                    <span>Thêm Vị Sốt Mới</span>
                </h3>
                <button type="button" @click="showAddSauceModal = false" class="text-gray-400 hover:text-gray-600 text-sm font-bold">✕</button>
            </div>

            <div class="space-y-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Tên vị sốt <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        x-model="newSauceName" 
                        placeholder="VD: Sốt Trứng Muối Hoàng Kim" 
                        class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Khẩu hiệu / Slogan ngắn</label>
                    <input 
                        type="text" 
                        x-model="newSauceTagline" 
                        placeholder="VD: Vị béo ngậy đậm đà trứng muối thật..." 
                        class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                    >
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Giá hũ bán thêm</label>
                    <div class="relative">
                        <input 
                            type="number" 
                            x-model="newSaucePrice" 
                            step="1000" 
                            min="0"
                            class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-red-600 focus:bg-white focus:border-red-500 outline-none pr-8 font-mono"
                        >
                        <span class="absolute right-3 top-2 text-xs text-gray-400 font-bold">đ</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button 
                    type="button" 
                    @click="showAddSauceModal = false"
                    class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors cursor-pointer"
                >
                    Đóng
                </button>
                <button 
                    type="button" 
                    @click="createSauce()"
                    :disabled="isAddingSauce"
                    class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer"
                >
                    <span x-show="!isAddingSauce">Lưu vị sốt</span>
                    <span x-show="isAddingSauce">Đang lưu...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 5. MODAL THÊM TOPPING MỚI -->
    <div 
        x-show="showAddToppingModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" 
        x-cloak
    >
        <div 
            @click.outside="showAddToppingModal = false"
            class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-4 border border-gray-100"
        >
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🍳</span>
                    <span>Thêm Topping Mới</span>
                </h3>
                <button type="button" @click="showAddToppingModal = false" class="text-gray-400 hover:text-gray-600 text-sm font-bold">✕</button>
            </div>

            <div class="space-y-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Tên topping <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        x-model="newToppingName" 
                        placeholder="VD: Rong Biển Rắc Mè" 
                        class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Giá bán thêm</label>
                    <div class="relative">
                        <input 
                            type="number" 
                            x-model="newToppingPrice" 
                            step="1000" 
                            min="0"
                            class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-red-600 focus:bg-white focus:border-red-500 outline-none pr-8 font-mono"
                        >
                        <span class="absolute right-3 top-2 text-xs text-gray-400 font-bold">đ</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button 
                    type="button" 
                    @click="showAddToppingModal = false"
                    class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors cursor-pointer"
                >
                    Đóng
                </button>
                <button 
                    type="button" 
                    @click="createTopping()"
                    :disabled="isAddingTopping"
                    class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer"
                >
                    <span x-show="!isAddingTopping">Lưu topping</span>
                    <span x-show="isAddingTopping">Đang lưu...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 6. STICKY BULK ACTION BAR (KHI CÓ THAY ĐỔI VỊ SỐT CHƯA LƯU) -->
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
