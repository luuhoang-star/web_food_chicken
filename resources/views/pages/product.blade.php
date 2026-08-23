@extends('layouts.app')

@section('title', $product->name . ' - Chi Tiết Món Ăn | GAO - Gà Sốt & Cơm Hà Nội')

@section('content')
<div class="py-8 sm:py-12 bg-[#FAF6F0]" x-data="{
    quantity: 1,
    selectedSauce: '{{ $product->default_sauce ?? "Sốt Cay Hàn" }}',
    selectedToppings: [],
    note: '',
    basePrice: {{ (int)$product->price }},
    
    toggleTopping(toppingId) {
        const idx = this.selectedToppings.indexOf(toppingId);
        if (idx > -1) {
            this.selectedToppings.splice(idx, 1);
        } else {
            this.selectedToppings.push(toppingId);
        }
    },

    get toppingTotal() {
        return this.selectedToppings.reduce((sum, topId) => {
            const top = this.availableToppings.find(t => t.id === topId);
            return sum + (top ? top.price : 0);
        }, 0);
    },

    get singlePrice() {
        return this.basePrice + this.toppingTotal;
    },

    get totalPrice() {
        return this.singlePrice * this.quantity;
    },

    addToCartCurrent() {
        const toppingNames = this.selectedToppings.map(id => {
            const t = this.availableToppings.find(item => item.id === id);
            return t ? t.name : '';
        }).filter(Boolean);

        const cartItem = {
            id: 'product-detail-' + Date.now(),
            item_type: 'product',
            product_id: {{ $product->id }},
            sauce_id: {{ $product->sauce_id ?? 'null' }},
            name: '{{ $product->name }}',
            price: this.singlePrice,
            quantity: this.quantity,
            sauce: {{ $product->requiresSauceChoice() ? 'this.selectedSauce' : json_encode($product->default_sauce ?? $product->sauce?->name ?? '') }},
            spiceLevel: null,
            toppings: toppingNames,
            note: this.note,
            image: '{{ $product->image }}'
        };

        this.cartItems.unshift(cartItem);
        this.saveCart();
        this.isCartOpen = true;
        this.showToast('Đã thêm vào giỏ hàng!', cartItem.name);
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between gap-4 mb-8">
            <nav class="flex items-center gap-2 text-xs font-bold text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('menu') }}" class="hover:text-red-600 transition-colors">Thực đơn</a>
                <span>/</span>
                <span class="text-red-600 font-extrabold">{{ $product->name }}</span>
            </nav>
            <a 
                href="{{ route('menu') }}" 
                class="inline-flex items-center gap-1.5 text-xs font-black text-gray-600 hover:text-red-600 cursor-pointer"
            >
                <span>← Quay lại menu</span>
            </a>
        </div>

        <!-- Main Product Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-orange-200/80 shadow-xl mb-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                
                <!-- Left: Big Image & Tags -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="relative rounded-2xl overflow-hidden aspect-square shadow-lg border-2 border-orange-100 bg-gray-900 group">
                        <img 
                            src="{{ $product->image }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        @if($product->tag)
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 rounded-md text-xs font-black uppercase text-white shadow-md {{ $product->tag === 'BEST SELLER' ? 'bg-red-600' : 'bg-amber-500' }}">
                                {{ $product->tag }}
                            </span>
                        </div>
                        @endif

                        <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-xs px-3 py-1 rounded-full text-xs font-extrabold text-gray-800 flex items-center gap-1 shadow-md border border-gray-100">
                            <span class="text-amber-400">⭐</span>
                            <span>{{ $product->rating }} ({{ $product->review_count ?? 384 }} đánh giá)</span>
                        </div>
                    </div>

                    <!-- Mini benefits under image -->
                    <div class="grid grid-cols-3 gap-2 text-center text-[11px] font-bold text-gray-600 pt-2">
                        <div class="p-2.5 rounded-xl bg-[#FAF6F0] border border-orange-100">
                            <span class="block text-base">⚡</span>
                            <span>Giao 25-40p</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-[#FAF6F0] border border-orange-100">
                            <span class="block text-base">🔥</span>
                            <span>Nóng giòn</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-[#FAF6F0] border border-orange-100">
                            <span class="block text-base">🍗</span>
                            <span>Đậm vị sốt</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Information & In-Page Customizer -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-black uppercase">
                                {{ $product->category->name ?? 'Món chính' }}
                            </span>
                            @if($product->subtag)
                            <span class="text-xs font-bold text-gray-500">
                                {{ $product->subtag }}
                            </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                            {{ $product->name }}
                        </h1>

                        <div class="mt-3 flex items-baseline gap-3">
                            <span class="text-3xl font-black text-red-600">
                                {{ number_format($product->price, 0, ',', '.') }}đ
                            </span>
                            @if($product->original_price)
                            <span class="text-sm font-bold text-gray-400 line-through">
                                {{ number_format($product->original_price, 0, ',', '.') }}đ
                            </span>
                            @endif
                        </div>

                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed mt-3">
                            {{ $product->description }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-6 space-y-5">
                        
                        <!-- 1. Chọn vị sốt (Bắt buộc nếu món là Combo / sauce_selection = required) -->
                        @if($product->requiresSauceChoice() || $product->category->slug === 'combo')
                        <div>
                            <label class="block text-xs font-black text-gray-900 uppercase tracking-wider mb-2.5">
                                1. CHỌN VỊ SỐT ĐẶC TRƯNG <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                <template x-for="sauce in sauceList" :key="sauce.id">
                                    <button 
                                        @click="selectedSauce = sauce.name" 
                                        type="button"
                                        class="p-3 rounded-xl border text-left transition-all flex items-center justify-between cursor-pointer"
                                        :class="selectedSauce === sauce.name 
                                            ? 'border-2 border-red-500 bg-[#FFF5F5] font-black text-gray-900 shadow-xs ring-2 ring-red-500/20' 
                                            : 'border-gray-200 bg-white hover:border-gray-300 text-gray-800'"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span x-text="sauce.icon">🌶️</span>
                                            <span class="text-xs font-bold" x-text="sauce.name">Sốt Cay Hàn</span>
                                        </div>
                                        <span x-show="selectedSauce === sauce.name" class="text-red-600 text-xs font-black">✓</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @endif

                        <!-- 2. Thêm Topping -->
                        <div>
                            <label class="block text-xs font-black text-gray-900 uppercase tracking-wider mb-2.5">
                                {{ ($product->requiresSauceChoice() || $product->category->slug === 'combo') ? '2. THÊM TOPPING HẢO HẠNG (TUỲ CHỌN)' : '1. THÊM TOPPING HẢO HẠNG (TUỲ CHỌN)' }}
                            </label>
                            <div class="space-y-2">
                                <template x-for="top in availableToppings" :key="top.id">
                                    <label 
                                        class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                        :class="selectedToppings.includes(top.id) 
                                            ? 'border-2 border-red-500 bg-[#FFF5F5]/60 shadow-xs' 
                                            : 'border-gray-200 bg-white hover:border-gray-300'"
                                    >
                                        <div class="flex items-center gap-3">
                                            <input 
                                                type="checkbox" 
                                                :checked="selectedToppings.includes(top.id)"
                                                @change="toggleTopping(top.id)"
                                                class="w-4 h-4 text-red-600 rounded-md border-gray-300 focus:ring-red-500 cursor-pointer"
                                            >
                                            <span class="text-sm" x-text="top.icon">🍳</span>
                                            <span class="text-xs font-bold text-gray-800" x-text="top.name">Trứng Ốp La</span>
                                        </div>
                                        <span class="text-xs font-black text-red-600" x-text="'+' + formatCurrency(top.price)">+10.000đ</span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Ghi chú cho quán -->
                        <div>
                            <label class="block text-xs font-black text-gray-900 uppercase tracking-wider mb-1.5">
                                GHI CHÚ CHO QUÁN
                            </label>
                            <textarea 
                                x-model="note"
                                rows="2"
                                placeholder="Ví dụ: Cơm nhiều, ít tương ớt, để riêng..."
                                class="w-full p-3 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                            ></textarea>
                        </div>

                        <!-- Quantity Stepper & Add To Cart Button -->
                        <div class="pt-4 flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex items-center gap-3 px-4 py-2.5 rounded-full border border-gray-200 bg-gray-50/80 w-full sm:w-auto justify-center">
                                <button 
                                    @click="if (quantity > 1) quantity--" 
                                    class="w-7 h-7 rounded-full bg-white border border-gray-200 font-bold text-xs flex items-center justify-center hover:bg-gray-100 text-gray-700 cursor-pointer"
                                >-</button>
                                <span class="font-black text-sm w-6 text-center text-gray-900" x-text="quantity">1</span>
                                <button 
                                    @click="quantity++" 
                                    class="w-7 h-7 rounded-full bg-white border border-gray-200 font-bold text-xs flex items-center justify-center hover:bg-gray-100 text-gray-700 cursor-pointer"
                                >+</button>
                            </div>

                            <button 
                                @click="addToCartCurrent()" 
                                type="button" 
                                class="flex-1 w-full py-4 px-8 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm tracking-wide shadow-lg red-glow transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <span>THÊM VÀO GIỎ</span>
                                <span>•</span>
                                <span x-text="formatCurrency(totalPrice)">49.000đ</span>
                            </button>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <!-- Cross-sell: GỢI Ý MÓN ĂN KÈM HỢP VỊ -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-orange-200/80">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🍟</span>
                    <h3 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight uppercase">
                        Gợi Ý Món Ăn Kèm Hợp Vị
                    </h3>
                </div>
                <a href="{{ route('menu') }}" class="text-xs font-black text-red-600 hover:underline">
                    Xem toàn bộ menu →
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <template x-for="item in upsellItems.slice(0, 4)" :key="item.id">
                    <div class="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                        <div class="space-y-2">
                            <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 cursor-pointer" @click="openCustomize(item)">
                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div>
                                <h4 class="font-black text-xs sm:text-sm text-gray-900 line-clamp-1 group-hover:text-red-600 transition-colors" x-text="item.name"></h4>
                                <span class="text-xs font-black text-red-600" x-text="formatCurrency(item.price)"></span>
                            </div>
                        </div>
                        <div class="pt-2">
                            <button 
                                @click="openCustomize(item)" 
                                type="button" 
                                class="w-full py-1.5 rounded-full bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-extrabold text-xs transition-colors cursor-pointer"
                            >
                                + Thêm
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>
@endsection
