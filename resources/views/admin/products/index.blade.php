@extends('layouts.admin')

@section('title', 'Thực Đơn Món Ăn & Giá Bán')
@section('page_title', '🍗 Thực Đơn Món Ăn & Giá Bán')

@section('content')
<div class="space-y-4" x-data="{
    selectedIds: [],
    allSelected: false,
    bulkActionType: '',
    csrfToken: '{{ csrf_token() }}',
    toastMsg: '',
    toastType: 'success',

    showToast(msg, type = 'success') {
        this.toastMsg = msg;
        this.toastType = type;
        setTimeout(() => { this.toastMsg = ''; }, 3000);
    },

    toggleSelectAll(event, productIds) {
        if (event.target.checked) {
            this.selectedIds = [...productIds];
            this.allSelected = true;
        } else {
            this.selectedIds = [];
            this.allSelected = false;
        }
    },

    updateSelectAllState(totalCount) {
        this.allSelected = (this.selectedIds.length === totalCount && totalCount > 0);
    },

    async savePrice(productId, newPrice, originalPrice, inputElem) {
        const val = parseFloat(newPrice);
        if (isNaN(val) || val < 0) {
            this.showToast('Giá bán không hợp lệ', 'error');
            return;
        }

        try {
            inputElem.classList.add('bg-amber-50');
            const res = await fetch(`/admin/products/${productId}/price`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({ price: val })
            });

            const data = await res.json();
            inputElem.classList.remove('bg-amber-50');

            if (res.ok && data.success) {
                inputElem.classList.add('border-emerald-500', 'bg-emerald-50');
                setTimeout(() => {
                    inputElem.classList.remove('border-emerald-500', 'bg-emerald-50');
                }, 1200);
                this.showToast(data.message || 'Đã lưu giá mới!');
            } else {
                this.showToast(data.message || 'Lỗi khi lưu giá', 'error');
            }
        } catch (e) {
            inputElem.classList.remove('bg-amber-50');
            this.showToast('Lỗi kết nối máy chủ', 'error');
        }
    },

    async toggleStatus(productId, currentStatus, btnElem) {
        try {
            btnElem.style.opacity = '0.5';
            const res = await fetch(`/admin/products/${productId}/toggle-availability`, {
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
                // Cập nhật DOM trực tiếp
                const isAvail = data.is_available;
                const dot = btnElem.querySelector('.status-dot');
                const text = btnElem.querySelector('.status-text');
                const row = document.getElementById('product-row-' + productId);

                if (isAvail) {
                    btnElem.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-all cursor-pointer';
                    if (dot) dot.className = 'status-dot w-2 h-2 rounded-full bg-emerald-500';
                    if (text) text.textContent = 'Đang bán';
                    if (row) row.classList.remove('opacity-60', 'bg-gray-50/40');
                } else {
                    btnElem.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200 transition-all cursor-pointer';
                    if (dot) dot.className = 'status-dot w-2 h-2 rounded-full bg-gray-400';
                    if (text) text.textContent = 'Hết món';
                    if (row) row.classList.add('opacity-60', 'bg-gray-50/40');
                }
            } else {
                this.showToast(data.message || 'Lỗi chuyển trạng thái', 'error');
            }
        } catch (e) {
            btnElem.style.opacity = '1';
            this.showToast('Lỗi kết nối máy chủ', 'error');
        }
    },

    submitBulk(action) {
        if (this.selectedIds.length === 0) return;
        if (action === 'delete' && !confirm(`Bạn có chắc chắn muốn xoá ${this.selectedIds.length} món đã chọn?`)) {
            return;
        }
        this.bulkActionType = action;
        this.$nextTick(() => {
            document.getElementById('bulk-action-form').submit();
        });
    }
}">

    <!-- FLOATING TOAST THÔNG BÁO TỰ ĐỘNG LƯU -->
    <div 
        x-show="toastMsg" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-20 md:bottom-6 right-6 z-50 px-4 py-2.5 rounded-2xl shadow-xl text-xs font-bold flex items-center gap-2 border"
        :class="toastType === 'success' ? 'bg-gray-900 text-white border-gray-700' : 'bg-rose-600 text-white border-rose-700'"
        x-cloak
    >
        <span x-text="toastType === 'success' ? '✅' : '❌'"></span>
        <span x-text="toastMsg"></span>
    </div>

    <!-- 1. THANH BỘ LỌC NHANH (DANH MỤC - TRẠNG THÁI - SẮP XẾP - TÌM KIẾM - THÊM MÓN) -->
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-200/80 shadow-xs">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-2.5">
            
            <div class="flex flex-wrap items-center gap-2 flex-1 min-w-[280px]">
                
                <!-- 1. Bộ lọc Danh mục -->
                <div class="relative">
                    <select 
                        name="category_id" 
                        onchange="this.form.submit()" 
                        class="pl-3 pr-8 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-bold text-gray-800 focus:bg-white focus:border-red-500 outline-none cursor-pointer appearance-none transition-colors"
                    >
                        <option value="all" {{ empty($selectedCategory) || $selectedCategory === 'all' ? 'selected' : '' }}>Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon ?? '📁' }} {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[10px] text-gray-400">▼</span>
                </div>

                <!-- 2. Bộ lọc Trạng thái -->
                <div class="relative">
                    <select 
                        name="status" 
                        onchange="this.form.submit()" 
                        class="pl-3 pr-8 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-bold text-gray-800 focus:bg-white focus:border-red-500 outline-none cursor-pointer appearance-none transition-colors"
                    >
                        <option value="all" {{ ($selectedStatus ?? 'all') === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                        <option value="available" {{ ($selectedStatus ?? '') === 'available' ? 'selected' : '' }}>🟢 Đang bán</option>
                        <option value="out_of_stock" {{ ($selectedStatus ?? '') === 'out_of_stock' ? 'selected' : '' }}>🔴 Hết món</option>
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
                        <option value="name_asc" {{ ($selectedSort ?? '') === 'name_asc' ? 'selected' : '' }}>Tên A → Z</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[10px] text-gray-400">▼</span>
                </div>

                <!-- 4. Ô Tìm kiếm nhanh -->
                <div class="relative flex-1 min-w-[180px] max-w-xs">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $search }}" 
                        placeholder="🔍 Tìm tên món..." 
                        class="w-full pl-3.5 pr-8 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-colors"
                    >
                    @if($search)
                        <a 
                            href="{{ route('admin.products.index', ['category_id' => $selectedCategory, 'status' => $selectedStatus, 'sort' => $selectedSort]) }}" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-600 text-xs font-bold"
                            title="Xoá tìm kiếm"
                        >✕</a>
                    @endif
                </div>

            </div>

            <!-- 5. Hiển thị / trang & Nút Thêm Món Mới -->
            <div class="flex items-center gap-2">
                <!-- Số lượng mỗi trang -->
                <div class="relative hidden sm:block">
                    <select 
                        name="per_page" 
                        onchange="this.form.submit()" 
                        class="pl-2.5 pr-6 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-bold text-gray-600 focus:bg-white focus:border-red-500 outline-none cursor-pointer appearance-none"
                        title="Số món hiển thị mỗi trang"
                    >
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15 / trang</option>
                        <option value="30" {{ ($perPage ?? 15) == 30 ? 'selected' : '' }}>30 / trang</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50 / trang</option>
                    </select>
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[9px] text-gray-400">▼</span>
                </div>

                <!-- Nút Thêm Món Mới (Gọn gàng & Nổi bật vừa phải) -->
                <a 
                    href="{{ route('admin.products.create') }}" 
                    class="px-3.5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 shrink-0"
                >
                    <span>+ Thêm Món Mới</span>
                </a>
            </div>

        </form>
    </div>

    <!-- 2. THANH THAO TÁC HÀNG LOẠT (BULK ACTION BAR - HIỆN KHI CÓ CHECKBOX) -->
    <div 
        x-show="selectedIds.length > 0" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="bg-gray-900 text-white px-4 py-2.5 rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-md text-xs font-bold"
        x-cloak
    >
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-md bg-red-600 text-white text-[11px] font-black" x-text="selectedIds.length"></span>
            <span>món đã được chọn:</span>
        </div>

        <div class="flex items-center gap-2">
            <button 
                type="button" 
                @click="submitBulk('available')"
                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white transition-colors cursor-pointer"
            >
                🟢 Mở bán
            </button>
            <button 
                type="button" 
                @click="submitBulk('out_of_stock')"
                class="px-3 py-1.5 rounded-xl bg-gray-700 hover:bg-gray-600 text-white transition-colors cursor-pointer"
            >
                🔴 Hết món
            </button>
            <button 
                type="button" 
                @click="submitBulk('delete')"
                class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white transition-colors cursor-pointer"
            >
                🗑️ Xoá
            </button>
            <button 
                type="button" 
                @click="selectedIds = []; allSelected = false"
                class="px-2 py-1.5 text-gray-400 hover:text-white"
            >
                ✕ Bỏ chọn
            </button>
        </div>
    </div>

    <!-- Form ẩn để submit Bulk Action -->
    <form id="bulk-action-form" action="{{ route('admin.products.bulk-action') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" :value="bulkActionType">
        <template x-for="id in selectedIds" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    <!-- 3. BẢNG DANH SÁCH THỰC ĐƠN TỐI ƯU GIAO DIỆN (3-GIÂY NHÌN NHANH) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="w-10 px-4 py-3.5 text-center">
                            <input 
                                type="checkbox" 
                                @change="toggleSelectAll($event, {{ json_encode($products->pluck('id')) }})" 
                                :checked="allSelected"
                                class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer"
                                title="Chọn tất cả món trên trang này"
                            >
                        </th>
                        <th class="px-4 py-3.5">Món Ăn</th>
                        <th class="px-4 py-3.5">Danh Mục</th>
                        <th class="px-4 py-3.5">Giá Bán (Tự Lưu)</th>
                        <th class="px-4 py-3.5 text-center">Đã Bán</th>
                        <th class="px-4 py-3.5">Trạng Thái</th>
                        <th class="px-4 py-3.5 text-right">Thao Tác</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    @forelse($products as $product)
                        @php
                            $tag = $product->getRawOriginal('tag');
                            $isCrucialTag = in_array(strtoupper((string) $tag), ['BEST SELLER', 'TIẾT KIỆM', 'COMBO HOT', 'HOT']);
                            $soldCount = (int) ($product->sold_count ?? 0);
                            
                            // Tạo chuỗi mô tả phụ: [Danh mục] · [Sốt]
                            $sublineParts = [];
                            if ($product->category) {
                                $sublineParts[] = $product->category->name;
                            }
                            if ($product->sauce_selection === 'required') {
                                $sublineParts[] = 'Khách tự chọn sốt';
                            } elseif ($product->sauce_selection === 'fixed' && $product->sauce) {
                                $sublineParts[] = 'Sốt ' . $product->sauce->name;
                            } elseif ($product->sauce_selection === 'none') {
                                $sublineParts[] = 'Không dùng sốt';
                            }
                            $sublineText = implode(' · ', $sublineParts);
                        @endphp

                        <tr 
                            id="product-row-{{ $product->id }}" 
                            class="hover:bg-gray-50/50 transition-colors {{ ! $product->is_available ? 'opacity-60 bg-gray-50/40' : '' }}"
                        >
                            
                            <!-- Checkbox -->
                            <td class="px-4 py-3.5 text-center">
                                <input 
                                    type="checkbox" 
                                    value="{{ $product->id }}" 
                                    x-model="selectedIds" 
                                    @change="updateSelectAllState({{ $products->count() }})"
                                    class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer"
                                >
                            </td>

                            <!-- Món ăn (Ảnh 48px + Tên Món nổi bật + Badge quan trọng + Subline) -->
                            <td class="px-4 py-3.5 min-w-[240px]">
                                <div class="flex items-center gap-3">
                                    <img 
                                        src="{{ $product->image_url }}" 
                                        alt="{{ $product->name }}" 
                                        class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0 bg-gray-100 shadow-2xs"
                                        loading="lazy"
                                    >
                                    <div class="space-y-0.5 min-w-0">
                                        
                                        <!-- Tên món nổi bật nhất -->
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-gray-900 text-sm leading-tight hover:text-red-600 transition-colors">
                                                {{ $product->name }}
                                            </span>
                                            
                                            <!-- Chỉ giữ badge quan trọng như BEST SELLER, TIẾT KIỆM -->
                                            @if($tag && $isCrucialTag)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase tracking-wide {{ strtoupper($tag) === 'BEST SELLER' ? 'bg-red-100 text-red-700 border border-red-200/60' : 'bg-amber-100 text-amber-800 border border-amber-200/60' }}">
                                                    {{ $tag }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Subline: Cơm Gà · Sốt Cay Hàn -->
                                        <p class="text-xs text-gray-500 font-medium truncate max-w-[280px]">
                                            {{ $sublineText }}
                                            @if($product->subtag)
                                                <span class="text-gray-400 text-[11px] font-normal">({{ $product->subtag }})</span>
                                            @endif
                                        </p>

                                    </div>
                                </div>
                            </td>

                            <!-- Danh mục -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-xs font-semibold text-gray-700">
                                    {{ $product->category->name ?? '—' }}
                                </span>
                            </td>

                            <!-- Giá Bán & Sửa Nhanh Tự Lưu (Enter / Blur) -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="space-y-0.5">
                                    <div class="relative inline-flex items-center">
                                        <input 
                                            type="number" 
                                            value="{{ (int) $product->price }}" 
                                            step="1000" 
                                            min="0"
                                            @keydown.enter.prevent="$event.target.blur()"
                                            @blur="savePrice({{ $product->id }}, $event.target.value, {{ (int) $product->price }}, $event.target)"
                                            class="w-28 pl-2.5 pr-6 py-1 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 focus:border-red-500 text-xs font-black text-gray-900 focus:text-red-600 outline-none text-right transition-all"
                                            title="Sửa giá và ấn Enter hoặc click ra ngoài để tự lưu"
                                        >
                                        <span class="absolute right-2 text-xs font-bold text-gray-400 pointer-events-none">₫</span>
                                    </div>

                                    @if($product->original_price && $product->original_price > $product->price)
                                        <div class="text-[10px] text-gray-400 line-through text-right pr-1">
                                            {{ number_format((float) $product->original_price, 0, ',', '.') }} ₫
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Đã Bán -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1 text-xs font-bold {{ $soldCount > 10 ? 'text-orange-700 font-black' : 'text-gray-600' }}">
                                    @if($soldCount >= 10)
                                        <span class="text-xs">🔥</span>
                                    @endif
                                    <span>{{ $soldCount }}</span>
                                </span>
                            </td>

                            <!-- Trạng Thái (Toggle 1-Chạm Nhỏ Gọn) -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <button 
                                    type="button" 
                                    @click="toggleStatus({{ $product->id }}, {{ $product->is_available ? 'true' : 'false' }}, $el)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all cursor-pointer {{ $product->is_available ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200' }}"
                                    title="Click để chuyển nhanh Đang bán ⇄ Hết món"
                                >
                                    <span class="status-dot w-2 h-2 rounded-full {{ $product->is_available ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    <span class="status-text">{{ $product->is_available ? 'Đang bán' : 'Hết món' }}</span>
                                </button>
                            </td>

                            <!-- Thao Tác Chi Tiết -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right space-x-1">
                                <a 
                                    href="{{ route('admin.products.edit', $product->id) }}" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition-colors"
                                    title="Sửa chi tiết món ăn"
                                >
                                    <span>✏️ Sửa</span>
                                </a>

                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá món \'{{ addslashes($product->name) }}\' khỏi thực đơn?');">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="p-1 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                        title="Xoá món"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400 text-xs">
                                Không tìm thấy món ăn nào phù hợp với bộ lọc tìm kiếm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <!-- Phân trang & Tổng số món -->
        @if($products->hasPages() || $products->total() > 0)
            <div class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
                <div>
                    Hiển thị <strong>{{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}</strong> trong tổng số <strong>{{ $products->total() }}</strong> món ăn
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        @endif

    </div>

</div>
@endsection
