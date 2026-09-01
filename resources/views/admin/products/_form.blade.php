@php
    $isEdit = isset($product) && $product->exists;
@endphp

<div 
    class="max-w-4xl space-y-5 pb-20" 
    x-data="{
        isDirty: false,
        imagePreview: '{{ $isEdit ? $product->image_url : '' }}',
        showAdvancedUrl: false,
        sauceSelection: '{{ old('sauce_selection', $product->sauce_selection ?? 'none') }}',
        price: '{{ (int) old('price', $product->price ?? 0) ?: '' }}',
        originalPrice: '{{ old('original_price', $product->original_price ?? '') ? (int) old('original_price', $product->original_price) : '' }}',
        
        formatMoney(val) {
            if (!val || isNaN(val)) return '';
            return new Intl.NumberFormat('vi-VN').format(val) + ' ₫';
        },

        markDirty() {
            this.isDirty = true;
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                    this.isDirty = true;
                };
                reader.readAsDataURL(file);
            }
        },

        handleDrop(event) {
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const fileInput = document.getElementById('productImageInput');
                if (fileInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                    this.isDirty = true;
                };
                reader.readAsDataURL(file);
            }
        }
    }"
    @input="markDirty()"
    @change="markDirty()"
>

    <!-- 1. HEADER (BREADCRUMB + TÊN TRANG + NÚT HUỶ / XOÁ) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                <a href="{{ route('admin.products.index') }}" class="hover:text-red-600 transition-colors">← Thực đơn món</a>
                <span>/</span>
                <span class="text-gray-900">{{ $isEdit ? 'Chỉnh sửa món' : 'Thêm món mới' }}</span>
            </div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight leading-tight">
                {{ $isEdit ? $product->name : 'Thêm Món Ăn Mới Vào Thực Đơn' }}
            </h1>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center">
            @if($isEdit)
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá món \'{{ $product->name }}\' khỏi thực đơn?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                        <span>🗑️</span>
                        <span>Xoá món</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Error Summary -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <p class="font-bold flex items-center gap-1.5">
                <span>⚠️</span>
                <span>Vui lòng kiểm tra lại các thông tin sau:</span>
            </p>
            <ul class="list-disc list-inside space-y-0.5 font-medium pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- MAIN FORM -->
    <form 
        id="productForm"
        action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}" 
        method="POST" 
        enctype="multipart/form-data" 
        class="space-y-4"
        @keydown.enter="if ($event.target.tagName !== 'TEXTAREA') { $event.preventDefault(); }"
    >
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- NHÓM 1: THÔNG TIN MÓN & GIÁ BÁN -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
            <div class="border-b border-gray-100 pb-2.5 flex items-center justify-between">
                <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>📌</span>
                    <span>1. Thông Tin Món & Giá Bán</span>
                </h3>
            </div>

            <div class="space-y-3">
                <!-- Tên món full width -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Tên món ăn <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $product->name ?? '') }}" 
                        placeholder="VD: Cơm Gà Sốt Cay Hàn" 
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                </div>

                <!-- Danh mục & Tag trên 1 hàng -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Danh mục món <span class="text-red-500">*</span></label>
                        <select 
                            name="category_id" 
                            required
                            class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none cursor-pointer"
                        >
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) old('category_id', $product->category_id ?? '') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} {{ ! $category->is_active ? '(Đang ẩn)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Huy hiệu món (Tag nổi bật)</label>
                        <input 
                            type="text" 
                            name="tag" 
                            value="{{ old('tag', $product->tag ?? '') }}" 
                            placeholder="VD: Bán Chạy, Món Mới, Cay Nhiều..." 
                            class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <!-- Giá bán & Giá gốc -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">
                            Giá Bán Thực Tế (VNĐ) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="price" 
                                x-model="price"
                                placeholder="55000" 
                                required 
                                step="1000" 
                                min="0"
                                class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-red-600 focus:bg-white focus:border-red-500 outline-none pr-8 font-mono"
                            >
                            <span class="absolute right-3 top-2 text-xs text-gray-400 font-bold">đ</span>
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium" x-show="price" x-text="formatMoney(price)"></p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">
                            Giá Gốc Niêm Yết (Gạch ngang)
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="original_price" 
                                x-model="originalPrice"
                                placeholder="65000" 
                                step="1000" 
                                min="0"
                                class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-600 focus:bg-white focus:border-red-500 outline-none pr-8 font-mono"
                            >
                            <span class="absolute right-3 top-2 text-xs text-gray-400 font-bold">đ</span>
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium" x-show="originalPrice" x-text="formatMoney(originalPrice)"></p>
                    </div>
                </div>

                <!-- Mô tả chi tiết món ăn -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Mô tả món ăn chi tiết</label>
                    <textarea 
                        name="description" 
                        rows="2" 
                        placeholder="Mô tả nguyên liệu, hương vị thơm ngon của món ăn để khách hàng thèm hơn..." 
                        class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                    >{{ old('description', $product->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- NHÓM 2: HÌNH ẢNH & TRẠNG THÁI BÁN -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
            <div class="border-b border-gray-100 pb-2.5 flex items-center justify-between">
                <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🖼️</span>
                    <span>2. Hình Ảnh & Trạng Thái Bán</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                
                <!-- Khối Upload Drag & Drop Ảnh -->
                <div class="md:col-span-7 space-y-2">
                    <label class="block text-xs font-bold text-gray-700">Hình ảnh món ăn</label>
                    
                    <div 
                        @dragover.prevent=""
                        @drop.prevent="handleDrop($event)"
                        class="border-2 border-dashed border-gray-200 hover:border-red-400 rounded-2xl p-4 text-center bg-gray-50/60 hover:bg-red-50/30 transition-all cursor-pointer relative"
                        onclick="document.getElementById('productImageInput').click();"
                    >
                        <input 
                            type="file" 
                            id="productImageInput" 
                            name="image_file" 
                            accept="image/*" 
                            class="hidden"
                            @change="handleFileSelect($event)"
                        >
                        
                        <div class="space-y-1.5">
                            <span class="text-2xl block">📸</span>
                            <p class="text-xs font-bold text-gray-700">
                                Kéo thả ảnh vào đây hoặc <span class="text-red-600 underline">chọn ảnh từ máy</span>
                            </p>
                            <p class="text-[10px] text-gray-400">
                                Định dạng hỗ trợ: JPG, PNG, WEBP (Tối đa 3MB, tỉ lệ vuông 1:1)
                            </p>
                        </div>
                    </div>

                    <!-- Nút mở nhập link ảnh trực tiếp -->
                    <div>
                        <button 
                            type="button" 
                            @click="showAdvancedUrl = !showAdvancedUrl" 
                            class="text-[11px] font-bold text-gray-500 hover:text-gray-800 transition-colors flex items-center gap-1 cursor-pointer"
                        >
                            <span x-text="showAdvancedUrl ? '▼' : '▶'"></span>
                            <span>Hoặc dán URL link ảnh online</span>
                        </button>

                        <div x-show="showAdvancedUrl" class="mt-2 space-y-1" x-cloak>
                            <input 
                                type="text" 
                                name="image" 
                                value="{{ old('image', $product->image ?? '') }}" 
                                placeholder="https://example.com/anh-mon-ga.jpg"
                                @input="imagePreview = $event.target.value"
                                class="w-full px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>
                    </div>
                </div>

                <!-- Preview Thumbnail Bên Cạnh -->
                <div class="md:col-span-5 flex flex-col items-center justify-center p-3 bg-gray-50 rounded-2xl border border-gray-100 min-h-[140px]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Xem trước ảnh hiển thị:</span>
                    
                    <div class="w-28 h-28 rounded-2xl overflow-hidden bg-white shadow-2xs border border-gray-200 flex items-center justify-center">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!imagePreview">
                            <span class="text-3xl text-gray-300">🍗</span>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Trạng thái bán & Bật/Tắt -->
            <div class="pt-2 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200/80 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-gray-800 block">Đang phục vụ / Còn hàng</span>
                        <span class="text-[10px] text-gray-500">Khách có thể đặt món này ngay</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_available" 
                            value="1" 
                            class="sr-only peer" 
                            {{ old('is_available', $product->is_available ?? true) ? 'checked' : '' }}
                        >
                        <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200/80 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-gray-800 block">Món Ăn Nổi Bật (Featured)</span>
                        <span class="text-[10px] text-gray-500">Ưu tiên đưa lên đầu trang chủ</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_featured" 
                            value="1" 
                            class="sr-only peer" 
                            {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                        >
                        <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>

            </div>
        </div>

        <!-- 3. STICKY SUBMIT BAR (CỐ ĐỊNH PHÍA DƯỚI) -->
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-2xl py-3 px-4 sm:px-8">
            <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
                
                <div class="flex items-center gap-2">
                    <span x-show="isDirty" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200/70 px-2.5 py-1 rounded-xl" x-cloak>
                        <span>●</span>
                        <span>Có thay đổi chưa lưu</span>
                    </span>
                </div>

                <div class="flex items-center gap-2.5">
                    <a 
                        href="{{ route('admin.products.index') }}" 
                        class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors"
                    >
                        Hủy
                    </a>

                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.01] active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span>💾</span>
                        <span>{{ $isEdit ? 'Lưu Thay Đổi' : 'Tạo Món Ăn' }}</span>
                    </button>
                </div>

            </div>
        </div>

    </form>

</div>
