@extends('layouts.admin')

@section('title', 'Quản Lý Combo Món Ăn')
@section('page_title', '📦 Quản Lý Combo Món Ăn')

@section('content')
<div class="space-y-4" x-data="{
    selectedDrawerCombo: null,
    activeMenuId: null,
    csrfToken: '{{ csrf_token() }}',
    toastMsg: '',

    showToast(msg) {
        this.toastMsg = msg;
        setTimeout(() => { this.toastMsg = ''; }, 3000);
    },

    openDrawer(comboData) {
        this.selectedDrawerCombo = comboData;
    },

    toggleMenu(id, event) {
        event.stopPropagation();
        this.activeMenuId = this.activeMenuId === id ? null : id;
    },

    async toggleStatus(comboId, btnElem, event) {
        if (event) event.stopPropagation();
        try {
            btnElem.style.opacity = '0.5';
            const res = await fetch(`/admin/combos/${comboId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            btnElem.style.opacity = '1';

            const data = await res.json();
            if (res.ok && data.success) {
                this.showToast(data.message);
                const isAvail = data.is_active;
                const dot = btnElem.querySelector('.status-dot');
                const text = btnElem.querySelector('.status-text');
                const row = document.getElementById('combo-row-' + comboId);

                if (isAvail) {
                    btnElem.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-all cursor-pointer';
                    if (dot) dot.className = 'status-dot w-2 h-2 rounded-full bg-emerald-500';
                    if (text) text.textContent = 'Mở bán';
                    if (row) row.classList.remove('opacity-60', 'bg-gray-50/40');
                } else {
                    btnElem.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200 transition-all cursor-pointer';
                    if (dot) dot.className = 'status-dot w-2 h-2 rounded-full bg-gray-400';
                    if (text) text.textContent = 'Tạm ngưng';
                    if (row) row.classList.add('opacity-60', 'bg-gray-50/40');
                }
            } else {
                this.showToast(data.message || 'Lỗi cập nhật trạng thái');
            }
        } catch (e) {
            btnElem.style.opacity = '1';
            this.showToast('Lỗi kết nối máy chủ');
        }
    }
}" @click="activeMenuId = null">

    <!-- FLOATING TOAST THÔNG BÁO TỰ ĐỘNG -->
    <div 
        x-show="toastMsg" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-20 md:bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-2.5 rounded-2xl shadow-xl text-xs font-bold flex items-center gap-2 border border-gray-700"
        x-cloak
    >
        <span>✅</span>
        <span x-text="toastMsg"></span>
    </div>

    <!-- 1. THANH BỘ LỌC & TÌM KIẾM NHANH (TÌM KIẾM - TRẠNG THÁI - SẮP XẾP - THÊM COMBO) -->
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-200/80 shadow-xs">
        <form action="{{ route('admin.combos.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-2.5">
            
            <div class="flex flex-wrap items-center gap-2 flex-1 min-w-[280px]">
                
                <!-- 1. Ô Tìm kiếm nhanh -->
                <div class="relative flex-1 min-w-[180px] max-w-xs">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $search }}" 
                        placeholder="🔍 Tìm tên combo, tag..." 
                        class="w-full pl-3.5 pr-8 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-colors"
                    >
                    @if($search)
                        <a 
                            href="{{ route('admin.combos.index', ['status' => $selectedStatus, 'sort' => $selectedSort]) }}" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-600 text-xs font-bold"
                            title="Xoá tìm kiếm"
                        >✕</a>
                    @endif
                </div>

                <!-- 2. Bộ lọc Trạng thái -->
                <div class="relative">
                    <select 
                        name="status" 
                        onchange="this.form.submit()" 
                        class="pl-3 pr-8 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-bold text-gray-800 focus:bg-white focus:border-red-500 outline-none cursor-pointer appearance-none transition-colors"
                    >
                        <option value="all" {{ ($selectedStatus ?? 'all') === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                        <option value="active" {{ ($selectedStatus ?? '') === 'active' ? 'selected' : '' }}>🟢 Mở bán</option>
                        <option value="inactive" {{ ($selectedStatus ?? '') === 'inactive' ? 'selected' : '' }}>⚪ Tạm ngưng</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[10px] text-gray-400">▼</span>
                </div>

                <!-- 3. Bộ lọc Sắp xếp -->
                <div class="relative">
                    <select 
                        name="sort" 
                        onchange="this.form.submit()" 
                        class="pl-3 pr-8 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-bold text-gray-800 focus:bg-white focus:border-red-500 outline-none cursor-pointer appearance-none transition-colors"
                    >
                        <option value="latest" {{ ($selectedSort ?? 'latest') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="best_seller" {{ ($selectedSort ?? '') === 'best_seller' ? 'selected' : '' }}>🔥 Bán chạy nhất</option>
                        <option value="price_asc" {{ ($selectedSort ?? '') === 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                        <option value="price_desc" {{ ($selectedSort ?? '') === 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[10px] text-gray-400">▼</span>
                </div>

            </div>

            <!-- Nút Thêm Combo Mới -->
            <a 
                href="{{ route('admin.combos.create') }}" 
                class="px-3.5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 shrink-0 cursor-pointer"
            >
                <span>+ Thêm Combo Mới</span>
            </a>

        </form>
    </div>

    <!-- 2. BẢNG DANH SÁCH COMBO TINH GỌN & DỄ QUÉT (HIERARCHY RÕ RÀNG) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">Combo Món Ăn</th>
                        <th class="px-4 py-3">Các Món Trong Combo</th>
                        <th class="px-4 py-3">Giá Bán & Tiết Kiệm</th>
                        <th class="px-4 py-3">Huy Hiệu</th>
                        <th class="px-4 py-3">Trạng Thái</th>
                        <th class="px-4 py-3 text-right">Thao Tác</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    @forelse($combos as $combo)
                        @php
                            $savingAmount = ($combo->original_price && $combo->original_price > $combo->price) ? ($combo->original_price - $combo->price) : 0;
                            $tag = $combo->tag;
                            $imageUrl = str_starts_with($combo->image, 'http') ? $combo->image : asset($combo->image);

                            $comboPayload = [
                                'id' => $combo->id,
                                'name' => $combo->name,
                                'subtag' => $combo->subtag,
                                'slug' => $combo->slug,
                                'description' => $combo->description,
                                'image' => $imageUrl,
                                'price' => number_format((float) $combo->price, 0, ',', '.') . ' ₫',
                                'original_price' => $combo->original_price ? number_format((float) $combo->original_price, 0, ',', '.') . ' ₫' : null,
                                'saving' => $savingAmount > 0 ? number_format((float) $savingAmount, 0, ',', '.') . ' ₫' : null,
                                'tag' => $tag,
                                'is_active' => (bool) $combo->is_active,
                                'edit_url' => route('admin.combos.edit', $combo->id),
                                'items' => $combo->items->map(fn($item) => [
                                    'name' => $item->item_name,
                                    'qty' => $item->quantity,
                                ])
                            ];
                        @endphp

                        <tr 
                            id="combo-row-{{ $combo->id }}"
                            class="hover:bg-gray-50/70 transition-colors cursor-pointer {{ ! $combo->is_active ? 'opacity-60 bg-gray-50/40' : '' }}"
                            @click="openDrawer({{ json_encode($comboPayload) }})"
                        >
                            
                            <!-- 1. Ảnh & Tên Combo nổi bật nhất (Bỏ slug khỏi bảng) -->
                            <td class="px-4 py-3 min-w-[220px]">
                                <div class="flex items-center gap-3">
                                    <img 
                                        src="{{ $imageUrl }}" 
                                        alt="{{ $combo->name }}" 
                                        class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0 bg-gray-100 shadow-2xs"
                                        loading="lazy"
                                    >
                                    <div class="space-y-0.5 min-w-0">
                                        <span class="font-bold text-gray-900 text-sm leading-tight block hover:text-red-600 transition-colors">
                                            {{ $combo->name }}
                                        </span>
                                        @if($combo->subtag)
                                            <span class="text-xs text-gray-500 font-medium block truncate max-w-[220px]">
                                                {{ $combo->subtag }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Các món trong combo (Rút gọn tối đa 3 món, >3 món hiển thị +X món khác) -->
                            <td class="px-4 py-3 min-w-[200px] max-w-[260px]">
                                <div class="space-y-0.5">
                                    @php
                                        $totalItems = $combo->items->count();
                                        $displayItems = $combo->items->take(2);
                                        $remainingCount = $totalItems - 2;
                                    @endphp

                                    @forelse($displayItems as $item)
                                        <div class="text-xs text-gray-800 font-medium truncate">
                                            <span class="font-bold font-mono text-red-600">{{ $item->quantity }}×</span>
                                            <span>{{ $item->item_name }}</span>
                                        </div>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Chưa chọn món chi tiết</span>
                                    @endforelse

                                    @if($remainingCount > 0)
                                        <span class="text-[11px] text-blue-600 font-bold hover:underline block pt-0.5">
                                            +{{ $remainingCount }} món khác (xem chi tiết)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 3. Giá bán & Giá gốc & Số tiền tiết kiệm cụ thể -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-black text-gray-900 text-xs sm:text-sm">
                                            {{ number_format((float) $combo->price, 0, ',', '.') }} ₫
                                        </span>
                                        @if($combo->original_price && $combo->original_price > $combo->price)
                                            <span class="text-[10px] text-gray-400 line-through font-medium">
                                                {{ number_format((float) $combo->original_price, 0, ',', '.') }} ₫
                                            </span>
                                        @endif
                                    </div>

                                    @if($savingAmount > 0)
                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            <span>Tiết kiệm {{ number_format((float) $savingAmount, 0, ',', '.') }} ₫</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- 4. Huy Hiệu (Tag) - Giữ 1 tag quan trọng nhất -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($tag)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wide {{ strtoupper($tag) === 'BEST SELLER' ? 'bg-red-100 text-red-700 border border-red-200/60' : 'bg-amber-100 text-amber-800 border border-amber-200/60' }}">
                                        {{ $tag }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-[11px]">—</span>
                                @endif
                            </td>

                            <!-- 5. Trạng thái (Toggle nhỏ 1-Click) -->
                            <td class="px-4 py-3 whitespace-nowrap" @click.stop>
                                <button 
                                    type="button" 
                                    @click="toggleStatus({{ $combo->id }}, $el, $event)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all cursor-pointer {{ $combo->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200' }}"
                                    title="Bấm để chuyển Mở bán ⇄ Tạm ngưng"
                                >
                                    <span class="status-dot w-2 h-2 rounded-full {{ $combo->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    <span class="status-text">{{ $combo->is_active ? 'Mở bán' : 'Tạm ngưng' }}</span>
                                </button>
                            </td>

                            <!-- 6. Thao Tác (✏️ Sửa + Menu ⋮) -->
                            <td class="px-4 py-3 whitespace-nowrap text-right space-x-1" @click.stop>
                                <a 
                                    href="{{ route('admin.combos.edit', $combo->id) }}" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition-colors"
                                    title="Chỉnh sửa combo"
                                >
                                    <span>✏️ Sửa</span>
                                </a>

                                <!-- Menu Dropdown ⋮ -->
                                <div class="inline-block relative text-left">
                                    <button 
                                        type="button" 
                                        @click="toggleMenu({{ $combo->id }}, $event)"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-900 font-bold transition-colors cursor-pointer"
                                        title="Tuỳ chọn khác"
                                    >
                                        ⋮
                                    </button>

                                    <!-- Dropdown Menu Box -->
                                    <div 
                                        x-show="activeMenuId === {{ $combo->id }}" 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        class="origin-top-right absolute right-0 mt-1 w-36 rounded-xl shadow-lg bg-white border border-gray-100 py-1 z-30 text-left text-xs font-medium"
                                        x-cloak
                                    >
                                        <button 
                                            type="button" 
                                            @click="openDrawer({{ json_encode($comboPayload) }}); activeMenuId = null"
                                            class="w-full px-3 py-1.5 text-left text-gray-700 hover:bg-gray-50 flex items-center gap-1.5"
                                        >
                                            <span>👁️ Xem chi tiết</span>
                                        </button>

                                        <a 
                                            href="{{ route('admin.combos.edit', $combo->id) }}" 
                                            class="w-full px-3 py-1.5 text-left text-gray-700 hover:bg-gray-50 flex items-center gap-1.5"
                                        >
                                            <span>✏️ Chỉnh sửa</span>
                                        </a>

                                        <form action="{{ route('admin.combos.destroy', $combo->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá combo \'{{ addslashes($combo->name) }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="w-full px-3 py-1.5 text-left text-rose-600 hover:bg-rose-50 flex items-center gap-1.5 font-bold"
                                            >
                                                <span>🗑️ Xoá combo</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400 text-xs">
                                Chưa tìm thấy combo nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <!-- 3. COMBO DETAIL DRAWER (SLIDE-OVER PANEL TRƯỢT TỪ BÊN PHẢI KHI CLICK COMBO) -->
    <div 
        x-show="selectedDrawerCombo" 
        class="fixed inset-0 z-50 overflow-hidden" 
        x-cloak
    >
        <!-- Backdrop -->
        <div 
            x-show="selectedDrawerCombo"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-xs" 
            @click="selectedDrawerCombo = null"
        ></div>

        <!-- Drawer Content -->
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div 
                x-show="selectedDrawerCombo"
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
                    <div class="space-y-0.5">
                        <h3 class="font-black text-base text-gray-900" x-text="selectedDrawerCombo?.name"></h3>
                        <span class="text-[10px] text-gray-400 font-mono" x-text="'slug: ' + selectedDrawerCombo?.slug"></span>
                    </div>

                    <button 
                        @click="selectedDrawerCombo = null" 
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold flex items-center justify-center transition-colors cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <!-- Drawer Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4 text-xs">
                    
                    <!-- Combo Image Cover -->
                    <div class="rounded-2xl overflow-hidden border border-gray-200 bg-gray-50 aspect-video">
                        <img :src="selectedDrawerCombo?.image" :alt="selectedDrawerCombo?.name" class="w-full h-full object-cover">
                    </div>

                    <!-- Pricing & Savings Banner -->
                    <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Giá bán ưu đãi:</span>
                            <span class="font-black text-red-600 text-base" x-text="selectedDrawerCombo?.price"></span>
                        </div>
                        <template x-if="selectedDrawerCombo?.original_price">
                            <div class="flex justify-between text-gray-500">
                                <span>Giá gốc (tổng món):</span>
                                <span class="line-through font-mono" x-text="selectedDrawerCombo?.original_price"></span>
                            </div>
                        </template>
                        <template x-if="selectedDrawerCombo?.saving">
                            <div class="flex justify-between items-center text-emerald-700 font-bold pt-1 border-t border-gray-200">
                                <span>Tiết kiệm cho khách:</span>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-black" x-text="selectedDrawerCombo?.saving"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Subtag & Description -->
                    <div class="space-y-1 border-b border-gray-100 pb-3">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Mô tả gói combo:</span>
                        <p class="font-bold text-gray-800" x-text="selectedDrawerCombo?.subtag"></p>
                        <p class="text-gray-600 leading-relaxed text-[11px]" x-text="selectedDrawerCombo?.description || 'Chưa có mô tả chi tiết.'"></p>
                    </div>

                    <!-- Items Included -->
                    <div class="space-y-2 border-b border-gray-100 pb-3">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Danh sách món trong combo:</span>
                        <div class="divide-y divide-gray-100">
                            <template x-for="(it, idx) in selectedDrawerCombo?.items" :key="idx">
                                <div class="py-2 first:pt-0 last:pb-0 flex items-center justify-between">
                                    <span class="font-bold text-gray-900" x-text="it.name"></span>
                                    <span class="px-2 py-0.5 rounded-md bg-red-50 text-red-700 font-mono font-black" x-text="it.qty + ' suất'"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tag & Status -->
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500">Huy hiệu hiển thị:</span>
                        <span class="px-2 py-0.5 rounded-md font-black uppercase text-[10px] bg-red-100 text-red-700" x-text="selectedDrawerCombo?.tag || '—'"></span>
                    </div>

                </div>

                <!-- Drawer Footer CTA -->
                <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <a 
                        :href="selectedDrawerCombo?.edit_url" 
                        class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-center font-bold text-xs transition-colors shadow-xs"
                    >
                        ✏️ Chỉnh Sửa Combo Này
                    </a>
                    <button 
                        type="button" 
                        @click="selectedDrawerCombo = null" 
                        class="px-4 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs"
                    >
                        Đóng
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
