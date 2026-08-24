@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Món: ' . $product->name)
@section('page_title', '🍗 Chỉnh Sửa Chi Tiết Món Ăn')

@section('content')
<div 
    class="max-w-4xl space-y-5 pb-20" 
    x-data="{
        isDirty: false,
        imagePreview: '{{ $product->image_url }}',
        showAdvancedUrl: false,
        sauceSelection: '{{ $product->sauce_selection ?? 'fixed' }}',
        price: '{{ (int) $product->price }}',
        originalPrice: '{{ $product->original_price ? (int) $product->original_price : '' }}',
        
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

    <!-- 1. HEADER TINH GỌN (BREADCRUMB + TÊN MÓN + XOÁ) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                <a href="{{ route('admin.products.index') }}" class="hover:text-red-600 transition-colors">← Thực đơn</a>
                <span>/</span>
                <span class="text-gray-900">Chỉnh sửa</span>
            </div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight leading-tight">
                {{ $product->name }}
            </h1>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center">
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá món \'{{ $product->name }}\' khỏi thực đơn?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                    <span>🗑️</span>
                    <span>Xoá món</span>
                </button>
            </form>
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
        id="productEditForm"
        action="{{ route('admin.products.update', $product->id) }}" 
        method="POST" 
        enctype="multipart/form-data" 
        class="space-y-4"
        @keydown.enter="if ($event.target.tagName !== 'TEXTAREA') { $event.preventDefault(); }"
    >
        @csrf
        @method('PUT')

        <!-- NHÓM 1: THÔNG TIN MÓN -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
            <div class="border-b border-gray-100 pb-2.5 flex items-center justify-between">
                <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>📌</span>
                    <span>1. Thông Tin Món</span>
                </h3>
            </div>

            <div class="space-y-3">
                <!-- Tên món full width -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Tên món ăn <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $product->name) }}" 
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
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Nhãn nổi bật (Tag)</label>
                        <input 
                            type="text" 
                            name="tag" 
                            value="{{ old('tag', $product->getRawOriginal('tag')) }}" 
                            placeholder="VD: BEST SELLER, MỚI..." 
                            class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <!-- Mô tả ngắn (Subtag) full width -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Mô tả ngắn dưới tên (Subtag)</label>
                    <input 
                        type="text" 
                        name="subtag" 
                        value="{{ old('subtag', $product->subtag) }}" 
                        placeholder="VD: 🍗 Cơm dẻo + Miếng gà sốt Cay Hàn" 
                        class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                </div>

                <!-- Mô tả chi tiết textarea thấp hơn -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Mô tả chi tiết</label>
                    <textarea 
                        name="description" 
                        rows="2" 
                        placeholder="Mô tả nguyên liệu, hương vị món ăn..." 
                        class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none leading-relaxed"
                    >{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- NHÓM 2: GIÁ & SỐT -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
            <div class="border-b border-gray-100 pb-2.5 flex items-center justify-between">
                <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🌶️</span>
                    <span>2. Giá Bán & Vị Sốt</span>
                </h3>
            </div>

            <div class="space-y-3">
                <!-- Cơ chế sốt & Vị sốt mặc định trên 1 hàng -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Cơ chế sốt <span class="text-red-500">*</span></label>
                        <select 
                            name="sauce_selection" 
                            x-model="sauceSelection"
                            class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none cursor-pointer"
                        >
                            <option value="fixed">Món có vị sốt cố định (Gà sốt Cay, Sốt Bơ Tỏi...)</option>
                            <option value="required">Khách tự chọn 1 trong 4 loại sốt khi đặt món</option>
                            <option value="none">Không dùng sốt (Món ăn kèm, Đồ uống...)</option>
                        </select>
                    </div>

                    <div class="space-y-1" x-show="sauceSelection !== 'none'">
                        <label class="block text-xs font-bold text-gray-700">Sốt mặc định / đi kèm</label>
                        <select 
                            name="sauce_id" 
                            class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none cursor-pointer"
                        >
                            <option value="">-- Không chọn --</option>
                            @foreach($sauces as $sauce)
                                <option value="{{ $sauce->id }}" {{ old('sauce_id', $product->sauce_id) == $sauce->id ? 'selected' : '' }}>
                                    {{ $sauce->name }} ({{ $sauce->tagline }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Giá bán & Giá gốc niêm yết -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Giá bán thực tế -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-700">Giá bán thực tế <span class="text-red-500">*</span></label>
                            <span class="text-xs font-black text-red-600 font-mono" x-text="formatMoney(price)"></span>
                        </div>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="price" 
                                x-model="price"
                                placeholder="49000" 
                                step="1000" 
                                min="0" 
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-red-600 focus:bg-white focus:border-red-500 outline-none pr-8"
                            >
                            <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-bold">đ</span>
                        </div>
                    </div>

                    <!-- Giá gốc gạch ngang -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-700">Giá gốc niêm yết (gạch giá)</label>
                            <span class="text-xs font-semibold text-gray-400 line-through font-mono" x-text="formatMoney(originalPrice)"></span>
                        </div>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="original_price" 
                                x-model="originalPrice"
                                placeholder="VD: 60000" 
                                step="1000" 
                                min="0" 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-500 focus:bg-white focus:border-red-500 outline-none pr-8"
                            >
                            <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-bold">đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NHÓM 3: HÌNH ẢNH & TRẠNG THÁI -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
            <div class="border-b border-gray-100 pb-2.5 flex items-center justify-between">
                <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🖼️</span>
                    <span>3. Hình Ảnh & Trạng Thái</span>
                </h3>
            </div>

            <div class="space-y-3">
                <!-- Khu vực Upload & Preview trực quan -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
                    
                    <!-- Preview ảnh bên trái -->
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-200 border border-gray-300 shrink-0 flex items-center justify-center shadow-2xs">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" alt="Preview ảnh món" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!imagePreview">
                                <span class="text-2xl text-gray-400">🍗</span>
                            </template>
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-gray-900 block">Ảnh hiện tại</span>
                            <span class="text-[10px] text-gray-400 block">Kích thước chuẩn vuông</span>
                        </div>
                    </div>

                    <!-- Dropzone & Nút chọn ảnh bên phải (chiếm 2 cột) -->
                    <div 
                        class="sm:col-span-2 border-2 border-dashed border-gray-300 hover:border-red-500 rounded-xl p-3.5 text-center transition-colors bg-gray-50/50 hover:bg-red-50/20 cursor-pointer"
                        @dragover.prevent
                        @drop.prevent="handleDrop($event)"
                        @click="document.getElementById('productImageInput').click()"
                    >
                        <input 
                            id="productImageInput"
                            type="file" 
                            name="image_file" 
                            accept="image/*"
                            @change="handleFileSelect($event)"
                            class="hidden"
                        >
                        <div class="flex items-center justify-center gap-2 text-xs font-bold text-gray-700">
                            <span class="text-sm">📁</span>
                            <span>Kéo ảnh vào đây hoặc</span>
                            <span class="text-red-600 underline">chọn ảnh từ máy</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-0.5">Hỗ trợ JPG, PNG, WEBP (Tối đa 5MB)</p>
                    </div>

                </div>

                <!-- Tùy chọn URL nâng cao (Ẩn gọn mặc định) -->
                <div class="pt-1">
                    <button 
                        type="button" 
                        @click="showAdvancedUrl = !showAdvancedUrl"
                        class="text-[11px] font-bold text-gray-500 hover:text-gray-900 flex items-center gap-1 transition-colors cursor-pointer"
                    >
                        <span x-text="showAdvancedUrl ? '▾ Thu gọn' : '▸ Tùy chọn nâng cao: Nhập link ảnh online (URL)'"></span>
                    </button>

                    <div x-show="showAdvancedUrl" x-transition class="mt-2 space-y-1" x-cloak>
                        <input 
                            type="text" 
                            name="image_url" 
                            value="{{ old('image_url', $product->image) }}" 
                            placeholder="https://images.unsplash.com/..." 
                            @input="if ($event.target.value) { imagePreview = $event.target.value; markDirty(); }"
                            class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <!-- Trạng thái & Thứ tự hiển thị tinh gọn -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 border-t border-gray-100">
                    <!-- Mở bán -->
                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-200/80 cursor-pointer hover:bg-emerald-50 transition-colors">
                        <input 
                            type="checkbox" 
                            name="is_available" 
                            value="1" 
                            {{ old('is_available', $product->is_available) ? 'checked' : '' }} 
                            class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500"
                        >
                        <span class="text-xs font-bold text-emerald-900">🟢 Mở bán (Còn hàng)</span>
                    </label>

                    <!-- Món Hot -->
                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-red-50/60 border border-red-200/80 cursor-pointer hover:bg-red-50 transition-colors">
                        <input 
                            type="checkbox" 
                            name="is_hot" 
                            value="1" 
                            {{ old('is_hot', $product->is_hot) ? 'checked' : '' }} 
                            class="w-4 h-4 rounded text-red-600 focus:ring-red-500"
                        >
                        <span class="text-xs font-bold text-red-900">🔥 Món hot trang chủ</span>
                    </label>

                    <!-- Thứ tự sắp xếp -->
                    <div class="flex items-center gap-2 p-1.5 px-3 rounded-xl bg-gray-50 border border-gray-200">
                        <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Thứ tự:</label>
                        <input 
                            type="number" 
                            name="order" 
                            value="{{ old('order', $product->order) }}" 
                            min="1"
                            class="w-full px-2 py-1 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-900 text-center outline-none focus:border-red-500"
                        >
                    </div>
                </div>
            </div>
        </div>

    </form>

    <!-- 4. FOOTER CỐ ĐỊNH (STICKY ACTION BAR) -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-2xl py-3 px-4 sm:px-8">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
            
            <!-- Trạng thái Form Dirty -->
            <div class="flex items-center gap-2">
                <span x-show="isDirty" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200/70 px-2.5 py-1 rounded-xl animate-pulse" x-cloak>
                    <span>●</span>
                    <span>Chưa lưu thay đổi</span>
                </span>
                <span x-show="!isDirty" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500">
                    <span>✓</span>
                    <span>Thông tin đã đồng bộ</span>
                </span>
            </div>

            <!-- Nút Action -->
            <div class="flex items-center gap-2.5">
                <a 
                    href="{{ route('admin.products.index') }}" 
                    class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors"
                >
                    Hủy
                </a>

                <button 
                    type="button" 
                    @click="document.getElementById('productEditForm').submit()"
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
