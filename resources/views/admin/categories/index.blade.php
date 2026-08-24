@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục Món Ăn')
@section('page_title', '📂 Quản Lý Danh Mục Món Ăn')

@section('content')
<div 
    class="max-w-5xl space-y-5 pb-20" 
    x-data="{
        showModal: false,
        toastMessage: '',
        searchQuery: '',
        categories: {{ json_encode($categories->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'icon' => $c->icon ?: '🍗',
            'order' => (int)$c->order,
            'is_active' => (bool)$c->is_active,
            'products_count' => (int)$c->products_count,
            'is_dirty' => false,
            'saved_name' => $c->name,
            'saved_icon' => $c->icon ?: '🍗',
            'saved_order' => (int)$c->order,
        ])) }},

        newCat: {
            name: '',
            icon: '🍗',
            order: {{ ($categories->max('order') ?? 0) + 1 }}
        },

        showToast(msg) {
            this.toastMessage = msg;
            setTimeout(() => { this.toastMessage = ''; }, 3500);
        },

        onCategoryChange(c) {
            c.is_dirty = (c.name !== c.saved_name || c.icon !== c.saved_icon || c.order !== c.saved_order);
        },

        async saveCategory(c) {
            if (!c.name.trim()) {
                alert('Tên danh mục không được để trống.');
                return;
            }
            try {
                const res = await fetch(`/admin/categories/${c.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: c.name,
                        icon: c.icon,
                        order: c.order,
                        is_active: c.is_active
                    })
                });
                const data = await res.json();
                if (data.success) {
                    c.saved_name = c.name;
                    c.saved_icon = c.icon;
                    c.saved_order = c.order;
                    c.is_dirty = false;
                    this.showToast(`Đã lưu danh mục '${c.name}'!`);
                }
            } catch (e) {
                alert('Có lỗi khi lưu danh mục.');
            }
        },

        async toggleCategory(c) {
            try {
                const res = await fetch(`/admin/categories/${c.id}/toggle`, {
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
                    this.showToast(`'${c.name}' -> ${data.status_label}`);
                }
            } catch (e) {
                alert('Không thể đổi trạng thái danh mục.');
            }
        },

        async deleteCategory(c) {
            if (c.products_count > 0) {
                alert(`Không thể xoá danh mục '${c.name}' vì đang có ${c.products_count} món ăn thuộc danh mục này.`);
                return;
            }
            if (!confirm(`Xoá danh mục '${c.name}'?`)) return;

            try {
                const res = await fetch(`/admin/categories/${c.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.categories = this.categories.filter(item => item.id !== c.id);
                    this.showToast(`Đã xoá danh mục '${c.name}'!`);
                } else {
                    alert(data.message || 'Không thể xoá danh mục.');
                }
            } catch (e) {
                alert('Có lỗi khi xoá danh mục.');
            }
        },

        async createCategory() {
            if (!this.newCat.name.trim()) {
                alert('Vui lòng nhập tên danh mục.');
                return;
            }
            try {
                const res = await fetch('{{ route('admin.categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newCat)
                });
                const data = await res.json();
                if (data.success) {
                    this.categories.push({
                        id: data.category.id,
                        name: data.category.name,
                        slug: data.category.slug,
                        icon: data.category.icon,
                        order: data.category.order,
                        is_active: data.category.is_active,
                        products_count: 0,
                        is_dirty: false,
                        saved_name: data.category.name,
                        saved_icon: data.category.icon,
                        saved_order: data.category.order
                    });
                    this.newCat.name = '';
                    this.newCat.icon = '🍗';
                    this.newCat.order += 1;
                    this.showModal = false;
                    this.showToast(`Đã tạo danh mục '${data.category.name}'!`);
                }
            } catch (e) {
                alert('Có lỗi khi tạo danh mục.');
            }
        },

        filteredCategories() {
            if (!this.searchQuery) return this.categories;
            const q = this.searchQuery.toLowerCase();
            return this.categories.filter(c => c.name.toLowerCase().includes(q) || c.slug.toLowerCase().includes(q));
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
        <span class="text-sm">📂</span>
        <span x-text="toastMessage"></span>
    </div>

    <!-- 1. HEADER (TITLE + 1-CLICK TẠO DANH MỤC) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-0.5">
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>📂 Danh Mục Món Ăn</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">
                Các tab phân loại món ăn hiển thị ngoài website trên thanh điều hướng & trang Thực Đơn.
            </p>
        </div>

        <button 
            type="button" 
            @click="showModal = true"
            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-sm hover:scale-[1.01] active:scale-95 transition-all cursor-pointer flex items-center gap-1.5 self-start sm:self-center"
        >
            <span>➕</span>
            <span>Thêm danh mục</span>
        </button>
    </div>

    <!-- 2. TOOLBAR TÌM KIẾM & THỐNG KÊ -->
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2 font-bold text-gray-700">
            <span class="px-2.5 py-1 rounded-xl bg-gray-100 font-mono">
                Tổng: <span x-text="categories.length"></span> danh mục · <span x-text="categories.filter(c => c.is_active).length"></span> đang hiển thị
            </span>
        </div>

        <div class="relative flex-1 max-w-xs">
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="🔍 Tìm danh mục..." 
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
    </div>

    <!-- 3. BẢNG DANH MỤC (INLINE EDIT, 1-CLICK TOGGLE, FULL-WIDTH) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-2">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-black border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3.5 w-24">Thứ tự</th>
                        <th class="px-4 py-3.5 w-16">Icon</th>
                        <th class="px-4 py-3.5">Tên Danh Mục & Đường dẫn</th>
                        <th class="px-4 py-3.5 text-center">Số Món</th>
                        <th class="px-4 py-3.5 text-center w-36">Hiển Thị Trên Menu</th>
                        <th class="px-4 py-3.5 text-right w-24">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    <template x-for="c in filteredCategories()" :key="c.id">
                        <tr class="hover:bg-gray-50/60 transition-colors" :class="!c.is_active ? 'opacity-60 bg-gray-50/30' : ''">
                            
                            <!-- Thứ tự sắp xếp -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input 
                                    type="number" 
                                    x-model="c.order" 
                                    @input="onCategoryChange(c)"
                                    @blur="if(c.is_dirty) saveCategory(c)"
                                    min="1"
                                    class="w-12 px-2 py-1 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 focus:border-red-500 text-xs font-bold text-center text-gray-900 outline-none"
                                >
                            </td>

                            <!-- Icon -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input 
                                    type="text" 
                                    x-model="c.icon" 
                                    @input="onCategoryChange(c)"
                                    @blur="if(c.is_dirty) saveCategory(c)"
                                    class="w-10 px-1 py-1 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 focus:border-red-500 text-base text-center outline-none"
                                >
                            </td>

                            <!-- Tên danh mục -->
                            <td class="px-4 py-3">
                                <div class="space-y-0.5 max-w-sm">
                                    <input 
                                        type="text" 
                                        x-model="c.name" 
                                        @input="onCategoryChange(c)"
                                        @blur="if(c.is_dirty) saveCategory(c)"
                                        @keydown.enter.prevent="if(c.is_dirty) saveCategory(c)"
                                        class="w-full px-2.5 py-1 rounded-lg bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 focus:border-red-500 text-xs font-black text-gray-900 outline-none"
                                    >
                                    <span class="text-[10px] text-gray-400 font-mono block pl-1" x-text="'slug: ' + c.slug"></span>
                                </div>
                            </td>

                            <!-- Số món thuộc danh mục -->
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a 
                                    :href="'/admin/products?category_id=' + c.id" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] transition-colors"
                                    title="Xem các món trong danh mục này"
                                >
                                    <span x-text="'🍗 ' + c.products_count + ' món'"></span>
                                </a>
                            </td>

                            <!-- Toggle Trạng Thái 1-Click -->
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <button 
                                    type="button" 
                                    @click="toggleCategory(c)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer shadow-2xs"
                                    :class="c.is_active ? 'bg-emerald-100 hover:bg-emerald-200 text-emerald-800 border border-emerald-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-500 border border-gray-200'"
                                >
                                    <span class="w-2 h-2 rounded-full" :class="c.is_active ? 'bg-emerald-600 animate-pulse' : 'bg-gray-400'"></span>
                                    <span x-text="c.is_active ? '🟢 Hiển thị' : '⚪ Đã ẩn'"></span>
                                </button>
                            </td>

                            <!-- Thao tác Xoá -->
                            <td class="px-4 py-3 whitespace-nowrap text-right space-x-1">
                                <button 
                                    type="button" 
                                    x-show="c.is_dirty"
                                    @click="saveCategory(c)"
                                    class="px-2.5 py-1 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-[11px] transition-colors cursor-pointer"
                                    x-cloak
                                >
                                    Lưu
                                </button>

                                <button 
                                    type="button" 
                                    @click="deleteCategory(c)"
                                    class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors cursor-pointer"
                                    title="Xoá danh mục"
                                >
                                    🗑️
                                </button>
                            </td>

                        </tr>
                    </template>

                    <template x-if="filteredCategories().length === 0">
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-400 text-xs">
                                Không tìm thấy danh mục nào.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. MODAL THÊM DANH MỤC MỚI -->
    <div 
        x-show="showModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            
            <div 
                x-show="showModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-xs" 
                @click="showModal = false"
            ></div>

            <div 
                x-show="showModal"
                x-transition:enter="ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-sm p-5 space-y-4 text-xs z-50"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-black text-base text-gray-900">➕ Thêm Danh Mục Mới</h3>
                    <button @click="showModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold flex items-center justify-center cursor-pointer">✕</button>
                </div>

                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tên danh mục <span class="text-red-500">*</span></label>
                        <input type="text" x-model="newCat.name" placeholder="VD: Cơm Gà Sốt..." required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Icon biểu tượng</label>
                            <input type="text" x-model="newCat.icon" placeholder="🍗" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-base text-center outline-none">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Thứ tự hiển thị</label>
                            <input type="number" x-model="newCat.order" min="1" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 outline-none text-center">
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs cursor-pointer">Hủy</button>
                    <button type="button" @click="createCategory()" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs cursor-pointer">Tạo danh mục</button>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
