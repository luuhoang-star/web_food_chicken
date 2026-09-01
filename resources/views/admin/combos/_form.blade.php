@php
    $isEdit = isset($combo) && $combo->exists;
    $defaultItems = $isEdit 
        ? $combo->items->map(fn($i) => [
            'item_name' => $i->item_name, 
            'quantity' => (int)$i->quantity, 
            'product_id' => $i->product_id ? (int)$i->product_id : ''
        ])
        : [
            ['item_name' => '1 Suất gà sốt tuỳ chọn', 'quantity' => 1, 'product_id' => ''],
            ['item_name' => '1 Cơm trắng dẻo thơm', 'quantity' => 1, 'product_id' => ''],
            ['item_name' => '1 Salad bắp cải mè rang', 'quantity' => 1, 'product_id' => ''],
            ['item_name' => '1 Coca/Pepsi ướp lạnh', 'quantity' => 1, 'product_id' => '']
        ];
    $currentImage = $isEdit ? (str_starts_with($combo->image, 'http') ? $combo->image : asset($combo->image)) : asset('images/combos/combo-1.jpg');
    $currentTag = old('tag', $isEdit ? $combo->tag : 'TIẾT KIỆM');
@endphp

<div 
    class="max-w-5xl space-y-5 pb-20" 
    x-data="{
        isDirty: false,
        imagePreview: '{{ $currentImage }}',
        showUrlInput: false,
        imageUrl: '{{ old('image', $isEdit ? $combo->image : '') }}',
        name: '{{ addslashes(old('name', $isEdit ? $combo->name : '')) }}',
        price: '{{ (int) old('price', $isEdit ? $combo->price : 69000) }}',
        originalPrice: '{{ old('original_price', $isEdit && $combo->original_price ? (int)$combo->original_price : 79000) }}',
        
        productsList: {{ json_encode($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name])) }},
        
        items: {{ json_encode(old('items', $defaultItems)) }},

        get savings() {
            const p = parseInt(this.price) || 0;
            const op = parseInt(this.originalPrice) || 0;
            return op > p ? (op - p) : 0;
        },

        formatMoney(val) {
            if (!val || isNaN(val)) return '';
            return new Intl.NumberFormat('vi-VN').format(val) + ' ₫';
        },

        markDirty() {
            this.isDirty = true;
        },

        addItem() {
            const firstProd = this.productsList.length > 0 ? this.productsList[0] : null;
            this.items.push({
                item_name: firstProd ? firstProd.name : 'Món mới',
                quantity: 1,
                product_id: firstProd ? firstProd.id : ''
            });
            this.isDirty = true;
        },

        removeItem(idx) {
            if (this.items.length <= 1) {
                alert('Combo phải có ít nhất 1 món ăn.');
                return;
            }
            if (confirm('Xoá món này khỏi gói combo?')) {
                this.items.splice(idx, 1);
                this.isDirty = true;
            }
        },

        updateQuantity(idx, delta) {
            const newQty = (parseInt(this.items[idx].quantity) || 1) + delta;
            if (newQty >= 1) {
                this.items[idx].quantity = newQty;
                this.isDirty = true;
            }
        },

        onProductSelect(idx, event) {
            const prodId = event.target.value;
            const found = this.productsList.find(p => p.id == prodId);
            if (found) {
                this.items[idx].item_name = found.name;
                this.items[idx].product_id = found.id;
            } else {
                this.items[idx].product_id = '';
            }
            this.isDirty = true;
        },

        handleFileChange(event) {
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
                const fileInput = document.getElementById('comboImageInput');
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

    <!-- 1. HEADER (BREADCRUMB + TÊN COMBO + XOÁ NẾU CÓ) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                <a href="{{ route('admin.combos.index') }}" class="hover:text-red-600 transition-colors">← Gói Combo</a>
                <span>/</span>
                <span class="text-gray-900">{{ $isEdit ? 'Chỉnh sửa' : 'Thêm mới' }}</span>
            </div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight leading-tight">
                {{ $isEdit ? $combo->name : 'Tạo Combo Món Ưu Đãi Mới' }}
            </h1>
        </div>

        @if($isEdit)
            <div class="flex items-center gap-2 self-start sm:self-center">
                <form action="{{ route('admin.combos.destroy', $combo->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá gói combo \'{{ $combo->name }}\'?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                        <span>🗑️</span>
                        <span>Xoá combo</span>
                    </button>
                </form>
            </div>
        @endif
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
        id="comboForm"
        action="{{ $isEdit ? route('admin.combos.update', $combo->id) : route('admin.combos.store') }}" 
        method="POST" 
        enctype="multipart/form-data" 
        class="space-y-4"
        @keydown.enter="if ($event.target.tagName !== 'TEXTAREA') { $event.preventDefault(); }"
    >
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- 2-COLUMN LAYOUT -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
            
            <!-- CỘT TRÁI: ẢNH COMBO + TRẠNG THÁI -->
            <div class="md:col-span-4 space-y-4">
                
                <!-- Card Ảnh Combo -->
                <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
                    <div class="border-b border-gray-100 pb-2">
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider">Ảnh Combo</h3>
                    </div>

                    <!-- Preview Ảnh -->
                    <div 
                        class="aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 border border-gray-200 relative group cursor-pointer"
                        @click="document.getElementById('comboImageInput').click()"
                        @dragover.prevent
                        @drop.prevent="handleDrop($event)"
                    >
                        <img :src="imagePreview" alt="Ảnh combo" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5 backdrop-blur-2xs">
                            <span>📁 Thay ảnh</span>
                        </div>
                    </div>

                    <!-- Action Upload / URL -->
                    <div class="space-y-2">
                        <input 
                            id="comboImageInput"
                            type="file" 
                            name="image_file" 
                            accept="image/*" 
                            @change="handleFileChange($event)"
                            class="hidden"
                        >
                        
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                @click="document.getElementById('comboImageInput').click()"
                                class="flex-1 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold transition-colors text-center cursor-pointer"
                            >
                                📁 Chọn ảnh từ máy
                            </button>
                            
                            <button 
                                type="button" 
                                @click="showUrlInput = !showUrlInput"
                                class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition-colors cursor-pointer"
                                title="Dán link ảnh online"
                            >
                                🔗 Dán URL
                            </button>
                        </div>

                        <!-- Dropdown Dán URL (Thu gọn) -->
                        <div x-show="showUrlInput" x-transition class="space-y-1 pt-1" x-cloak>
                            <input 
                                type="text" 
                                name="image" 
                                x-model="imageUrl"
                                @input="if (imageUrl) { imagePreview = imageUrl; markDirty(); }"
                                placeholder="https://images.unsplash.com/..."
                                class="w-full px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>
                    </div>
                </div>

                <!-- Card Trạng Thái -->
                <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs space-y-2.5">
                    <div class="border-b border-gray-100 pb-2">
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider">Trạng Thái</h3>
                    </div>

                    <!-- Toggle: Đang bán -->
                    <label class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-200/80 cursor-pointer hover:bg-emerald-50 transition-colors">
                        <span class="text-xs font-bold text-emerald-900">🟢 Đang mở bán</span>
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1" 
                            {{ old('is_active', $isEdit ? $combo->is_active : true) ? 'checked' : '' }} 
                            class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500"
                        >
                    </label>

                    <!-- Thứ tự sắp xếp -->
                    <div class="flex items-center justify-between p-2 px-3 rounded-xl bg-gray-50 border border-gray-200 text-xs">
                        <span class="font-bold text-gray-700">Thứ tự hiển thị:</span>
                        <input 
                            type="number" 
                            name="order" 
                            value="{{ old('order', $isEdit ? ($combo->order ?? 1) : 1) }}" 
                            min="1"
                            class="w-16 px-2 py-1 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-900 text-center outline-none focus:border-red-500"
                        >
                    </div>
                </div>

            </div>

            <!-- CỘT PHẢI: THÔNG TIN COMBO + DANH SÁCH MÓN -->
            <div class="md:col-span-8 space-y-4">
                
                <!-- CARD 1: THÔNG TIN COMBO -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
                    <div class="border-b border-gray-100 pb-2">
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider">Thông Tin Combo</h3>
                    </div>

                    <div class="space-y-3">
                        <!-- Tên Combo full width -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Tên Combo <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                name="name" 
                                x-model="name"
                                value="{{ old('name', $isEdit ? $combo->name : '') }}"
                                placeholder="VD: Combo 1 Người Ăn No"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>

                        <!-- Giá Bán & Giá Gốc (Kèm tính tiết kiệm tự động) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Giá Bán -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-gray-700">Giá bán ưu đãi <span class="text-red-500">*</span></label>
                                    <span class="text-xs font-black text-red-600 font-mono" x-text="formatMoney(price)"></span>
                                </div>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        name="price" 
                                        x-model="price"
                                        placeholder="69000" 
                                        required
                                        step="1000"
                                        min="0"
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-red-600 focus:bg-white focus:border-red-500 outline-none pr-8"
                                    >
                                    <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-bold">đ</span>
                                </div>
                            </div>

                            <!-- Giá Gốc -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-gray-700">Giá gốc niêm yết</label>
                                    <span class="text-xs font-semibold text-gray-400 line-through font-mono" x-text="formatMoney(originalPrice)"></span>
                                </div>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        name="original_price" 
                                        x-model="originalPrice"
                                        placeholder="VD: 79000" 
                                        step="1000" 
                                        min="0"
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-600 focus:bg-white focus:border-red-500 outline-none pr-8"
                                    >
                                    <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-bold">đ</span>
                                </div>
                            </div>
                        </div>

                        <!-- Badge Tiết Kiệm Tự Động -->
                        <div x-show="savings > 0" class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-200/80 flex items-center justify-between text-xs font-bold text-emerald-800" x-cloak>
                            <span>Khách tiết kiệm được khi mua combo:</span>
                            <span class="font-black text-sm text-emerald-700" x-text="'Tiết kiệm ' + formatMoney(savings)"></span>
                        </div>

                        <!-- Tiêu Đề Phụ (Subtag) & Huy Hiệu (Tag) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Tiêu đề phụ (Subtag)</label>
                                <input 
                                    type="text" 
                                    name="subtag" 
                                    value="{{ old('subtag', $isEdit ? $combo->subtag : '🍱 Dành cho 1 người') }}"
                                    placeholder="VD: 🍱 Dành cho 1 người" 
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                                >
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Huy hiệu (Tag nổi bật)</label>
                                <select name="tag" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-800 outline-none cursor-pointer">
                                    <option value="TIẾT KIỆM" {{ $currentTag === 'TIẾT KIỆM' ? 'selected' : '' }}>🏷️ TIẾT KIỆM (Tiết kiệm)</option>
                                    <option value="BEST SELLER" {{ $currentTag === 'BEST SELLER' ? 'selected' : '' }}>🔥 BEST SELLER (Bán chạy)</option>
                                    <option value="" {{ empty($currentTag) ? 'selected' : '' }}>-- Không gắn nhãn --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Mô Tả Combo -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Mô tả combo</label>
                            <textarea 
                                name="description" 
                                rows="2" 
                                placeholder="Ghi chú thành phần, mô tả hương vị của combo..."
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none leading-relaxed"
                            >{{ old('description', $isEdit ? $combo->description : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: DANH SÁCH MÓN TRONG COMBO -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
                    <div class="border-b border-gray-100 pb-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-wider">Món Trong Combo</h3>
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-xs font-bold" x-text="items.length + ' món'"></span>
                        </div>

                        <button 
                            type="button" 
                            @click="addItem()"
                            class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer"
                        >
                            <span>+ Thêm món</span>
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-2.5">
                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                                
                                <!-- Dropdown Chọn Món Có Sẵn Trong Menu -->
                                <div class="w-full sm:w-48 shrink-0">
                                    <select 
                                        @change="onProductSelect(idx, $event)"
                                        class="w-full px-2.5 py-2 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-900 outline-none cursor-pointer"
                                    >
                                        <option value="">-- Món tuỳ chỉnh --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" :selected="item.product_id == {{ $prod->id }}">{{ $prod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Input Tên hiển thị / Option trong Combo -->
                                <div class="flex-1 min-w-0">
                                    <input 
                                        type="text" 
                                        :name="'items[' + idx + '][item_name]'" 
                                        x-model="item.item_name"
                                        placeholder="Tên hiển thị (VD: Cơm gà sốt (Tuỳ chọn vị))" 
                                        required
                                        class="w-full px-3 py-2 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-900 outline-none focus:border-red-500"
                                    >
                                    <input type="hidden" :name="'items[' + idx + '][product_id]'" :value="item.product_id">
                                </div>

                                <!-- Stepper Số Lượng [-] [qty] [+] -->
                                <div class="flex items-center gap-1 bg-white p-1 rounded-lg border border-gray-200 shrink-0 self-start sm:self-center">
                                    <button 
                                        type="button" 
                                        @click="updateQuantity(idx, -1)"
                                        class="w-6 h-6 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center justify-center cursor-pointer"
                                    >
                                        −
                                    </button>
                                    
                                    <input 
                                        type="number" 
                                        :name="'items[' + idx + '][quantity]'" 
                                        x-model="item.quantity"
                                        min="1"
                                        required
                                        class="w-10 text-center font-mono font-black text-xs text-gray-900 outline-none p-0 border-0"
                                    >

                                    <button 
                                        type="button" 
                                        @click="updateQuantity(idx, 1)"
                                        class="w-6 h-6 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center justify-center cursor-pointer"
                                    >
                                        +
                                    </button>
                                </div>

                                <!-- Nút Xoá Dòng Món -->
                                <button 
                                    type="button" 
                                    @click="removeItem(idx)"
                                    class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors shrink-0 cursor-pointer self-end sm:self-center"
                                    title="Xoá món này khỏi combo"
                                >
                                    🗑️
                                </button>
                            </div>
                        </template>
                    </div>

                </div>

            </div>

        </div>

        <!-- 3. FOOTER CỐ ĐỊNH (STICKY ACTION BAR) -->
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-2xl py-3 px-4 sm:px-8">
            <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
                
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
                        href="{{ route('admin.combos.index') }}" 
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors"
                    >
                        Hủy
                    </a>

                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.01] active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span>💾</span>
                        <span>{{ $isEdit ? 'Lưu Combo' : 'Tạo Combo Mới' }}</span>
                    </button>
                </div>

            </div>
        </div>

    </form>

</div>
