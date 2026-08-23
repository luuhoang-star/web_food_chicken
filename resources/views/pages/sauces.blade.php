@extends('layouts.app')

@section('title', 'Bộ Sưu Tập Vị Sốt & Đặt Món Theo Sốt | GAO - Gà Sốt & Cơm Hà Nội')

@section('content')
<div class="py-8 sm:py-12 bg-[#FAF6F0]" x-data="{
    activeSauceSlug: '{{ $currentSauce->slug ?? "korean_spicy" }}',
    sauceFlavorProfiles: {
        'korean_spicy': { spicy: 4, sweet: 2, aroma: 4, richness: 3, bestFor: 'Tín đồ ăn cay, thích vị đậm đà chuẩn Hàn' },
        'honey_butter': { spicy: 0, sweet: 4, aroma: 3, richness: 4, bestFor: 'Trẻ nhỏ, gia đình, người thích vị ngọt béo dịu' },
        'garlic_butter': { spicy: 1, sweet: 2, aroma: 5, richness: 4, bestFor: 'Bữa trưa công sở, người mê tỏi phi giòn rụm' },
        'sweet_sour': { spicy: 1, sweet: 3, aroma: 4, richness: 2, bestFor: 'Người thích thanh mát, giải ngấy, bắt vị chua ngọt' }
    },
    get activeSauceData() {
        return this.sauceList.find(s => s.id === this.activeSauceSlug) || this.sauceList[0] || {};
    },
    get activeSauceFlavor() {
        return this.sauceFlavorProfiles[this.activeSauceSlug] || { spicy: 3, sweet: 3, aroma: 3, richness: 3, bestFor: 'Mọi bữa ăn trong ngày' };
    },
    get dishesForActiveSauce() {
        const sauce = this.activeSauceData;
        if (!sauce) return this.allMenuItems;
        
        // Filter items that match sauce name or are rice / chicken / combo categories
        return this.allMenuItems.filter(item => {
            if (item.sauce && item.sauce.toLowerCase().includes(sauce.name.toLowerCase())) return true;
            if (item.name.toLowerCase().includes(sauce.name.toLowerCase())) return true;
            if (['rice', 'chicken', 'combo'].includes(item.category)) return true;
            return false;
        });
    },
    setSauce(slug) {
        this.activeSauceSlug = slug;
        this.selectedSauceId = slug;
        // Optionally update URL without reload
        if (window.history.pushState) {
            window.history.pushState(null, '', '/sauces/' + slug);
        }
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs & Navigation -->
        <div class="flex items-center justify-between gap-4 mb-8">
            <nav class="flex items-center gap-2 text-xs font-bold text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Trang chủ</a>
                <span>/</span>
                <span class="text-red-600 font-extrabold">Bộ sưu tập Vị Sốt</span>
            </nav>
            <a 
                href="{{ route('menu') }}" 
                class="inline-flex items-center gap-1.5 text-xs font-black text-gray-700 hover:text-red-600 bg-white px-4 py-2 rounded-full border border-gray-200 shadow-xs hover:border-red-300 transition-colors"
            >
                <span>Xem thực đơn đầy đủ</span>
                <span>→</span>
            </a>
        </div>

        <!-- Page Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-black uppercase tracking-wider">
                <span>🍗</span>
                <span>CHUYÊN TRANG VỊ SỐT GAO</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 tracking-tight uppercase">
                CHỌN VỊ SỐT • KHÁM PHÁ MÓN ĂN
            </h1>
            <p class="text-gray-600 text-sm sm:text-base font-medium leading-relaxed">
                Mỗi vị sốt là một linh hồn riêng. Hãy bấm chọn từng loại sốt dưới đây để tìm hiểu tầng hương vị và thưởng thức các món ăn hảo hạng kết hợp cùng vị sốt đó.
            </p>
        </div>

        <!-- 4 SAUCES INTERACTIVE SELECTOR TABS -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-10">
            <template x-for="sauce in sauceList" :key="sauce.id">
                <button 
                    @click="setSauce(sauce.id)" 
                    type="button"
                    class="p-4 sm:p-5 rounded-2xl text-left border-2 transition-all duration-200 flex flex-col justify-between relative overflow-hidden group"
                    :class="activeSauceSlug === sauce.id 
                        ? 'border-red-600 bg-white shadow-xl ring-4 ring-red-500/10 -translate-y-1' 
                        : 'border-orange-100 bg-white/80 hover:bg-white hover:border-orange-300 shadow-xs'"
                >
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl" x-text="sauce.icon">🌶️</span>
                        <span 
                            x-show="activeSauceSlug === sauce.id" 
                            class="w-2.5 h-2.5 rounded-full bg-red-600 animate-ping"
                        ></span>
                    </div>

                    <div>
                        <h2 class="text-base sm:text-lg font-black text-gray-900 group-hover:text-red-600 transition-colors" x-text="sauce.name">
                            Sốt Cay Hàn
                        </h2>
                        <p class="text-xs font-bold text-amber-700 mt-0.5 line-clamp-1" x-text="sauce.shortDesc">
                            Cay nhẹ, ngọt hậu
                        </p>
                    </div>

                    <!-- Selected badge indicator -->
                    <div 
                        x-show="activeSauceSlug === sauce.id" 
                        class="mt-3 pt-2 border-t border-red-100 flex items-center gap-1 text-[11px] font-black text-red-600 uppercase"
                    >
                        <span>✓ Đang chọn sốt này</span>
                    </div>
                    <div 
                        x-show="activeSauceSlug !== sauce.id" 
                        class="mt-3 pt-2 border-t border-gray-100 flex items-center gap-1 text-[11px] font-bold text-gray-400 group-hover:text-gray-600"
                    >
                        <span>Bấm để xem món</span>
                    </div>
                </button>
            </template>
        </div>

        <!-- SELECTED SAUCE DEEP-DIVE SPOTLIGHT BOX -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-orange-200/80 shadow-lg mb-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                
                <!-- Sauce Big Image & Visual -->
                <div class="lg:col-span-5">
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-lg border-4 border-[#FAF6F0] bg-gray-900 group">
                        <img 
                            :src="activeSauceData.image" 
                            :alt="activeSauceData.name" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div class="absolute top-4 left-4 bg-black/75 backdrop-blur-md text-white text-xs font-extrabold px-3 py-1.5 rounded-full flex items-center gap-2">
                            <span x-text="activeSauceData.icon">🌶️</span>
                            <span x-text="activeSauceData.name">Sốt Cay Hàn</span>
                        </div>

                        <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-sm text-red-600 text-xs font-black px-3.5 py-1.5 rounded-full shadow-md">
                            <span>Giá tiêu chuẩn: </span>
                            <span x-text="formatCurrency(activeSauceData.price)">49.000đ</span>
                        </div>
                    </div>
                </div>

                <!-- Sauce In-depth Profile & Flavor Matrix -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-black uppercase tracking-wider" x-text="activeSauceData.tag">
                            🌶️ Vị cay đặc trưng
                        </span>
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                            ✨ Sốt thủ công độc quyền
                        </span>
                    </div>

                    <div>
                        <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                            <span class="text-red-600" x-text="activeSauceData.name">Sốt Cay Hàn</span>
                        </h2>
                        <p class="text-base sm:text-lg font-bold text-gray-700 mt-1" x-text="activeSauceData.subtitle">
                            Cay nhẹ, ngọt hậu, thơm nồng ớt Gochujang Hàn Quốc.
                        </p>
                    </div>

                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed" x-text="activeSauceData.description">
                        Là sốt đặc trưng làm nên thương hiệu GAO. Gà giòn tan quyện cùng nước sốt sánh mịn óng ả, phủ đều từng thớ thịt.
                    </p>

                    <!-- Flavor Matrix Grid -->
                    <div class="bg-[#FAF6F0] rounded-2xl p-5 border border-orange-100 space-y-3">
                        <h3 class="text-xs font-black text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span>📊</span>
                            <span>TẦNG HƯƠNG VỊ ĐẶC TRƯNG</span>
                        </h3>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <!-- Spiciness -->
                            <div class="bg-white p-3 rounded-xl border border-gray-200/80">
                                <span class="text-gray-500 font-bold block mb-1">Độ Cay</span>
                                <div class="flex text-amber-500 font-black">
                                    <template x-for="i in 5">
                                        <span :class="i <= activeSauceFlavor.spicy ? 'text-red-500' : 'text-gray-200'">🌶️</span>
                                    </template>
                                </div>
                            </div>
                            <!-- Sweetness -->
                            <div class="bg-white p-3 rounded-xl border border-gray-200/80">
                                <span class="text-gray-500 font-bold block mb-1">Độ Ngọt</span>
                                <div class="flex text-amber-500 font-black">
                                    <template x-for="i in 5">
                                        <span :class="i <= activeSauceFlavor.sweet ? 'text-amber-500' : 'text-gray-200'">🍯</span>
                                    </template>
                                </div>
                            </div>
                            <!-- Aroma -->
                            <div class="bg-white p-3 rounded-xl border border-gray-200/80">
                                <span class="text-gray-500 font-bold block mb-1">Hương Thơm</span>
                                <div class="flex text-amber-500 font-black">
                                    <template x-for="i in 5">
                                        <span :class="i <= activeSauceFlavor.aroma ? 'text-amber-500' : 'text-gray-200'">⭐</span>
                                    </template>
                                </div>
                            </div>
                            <!-- Richness -->
                            <div class="bg-white p-3 rounded-xl border border-gray-200/80">
                                <span class="text-gray-500 font-bold block mb-1">Độ Béo / Đậm</span>
                                <div class="flex text-amber-500 font-black">
                                    <template x-for="i in 5">
                                        <span :class="i <= activeSauceFlavor.richness ? 'text-amber-500' : 'text-gray-200'">🧈</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 text-xs font-semibold text-gray-700 flex items-center gap-2">
                            <span class="text-red-600 font-bold">Thích hợp nhất:</span>
                            <span x-text="activeSauceFlavor.bestFor">Tín đồ ăn cay, thích vị đậm đà chuẩn Hàn</span>
                        </div>
                    </div>

                    <!-- Quick CTA to customize -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button 
                            @click="openCustomizeFromSauce(activeSauceData)" 
                            type="button"
                            class="px-8 py-3.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-sm tracking-wide shadow-md red-glow transition-all active:scale-95 flex items-center justify-center gap-2"
                        >
                            <span>🍗 ĐẶT CƠM GÀ VỚI SỐT NÀY</span>
                            <span>→</span>
                        </button>
                    </div>

                </div>

            </div>
        </div>

        <!-- DISHES PAIRING WITH THIS SELECTED SAUCE -->
        <div class="space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-orange-200/80 pb-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-red-50 text-red-600 text-xs font-black uppercase mb-1">
                        <span>🍽️</span>
                        <span>DANH SÁCH MÓN ĂN</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                        CÁC MÓN ĂN VỚI <span class="text-red-600" x-text="activeSauceData.name">Sốt Cay Hàn</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
                        Chọn món bên dưới để tuỳ chỉnh độ cay, topping và thêm vào giỏ hàng ngay.
                    </p>
                </div>
                <div class="text-xs font-bold text-gray-500">
                    Hiển thị <strong class="text-red-600" x-text="dishesForActiveSauce.length"></strong> món ăn tương thích
                </div>
            </div>

            <!-- Dishes Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <template x-for="dish in dishesForActiveSauce" :key="dish.id">
                    <div class="bg-white rounded-2xl overflow-hidden border border-orange-100 hover:border-red-400 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                        
                        <!-- Thumbnail -->
                        <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 cursor-pointer" @click="openCustomize(dish)">
                            <img 
                                :src="dish.image" 
                                :alt="dish.name" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <!-- Tag -->
                            <div class="absolute top-3 left-3" x-show="dish.tag">
                                <span 
                                    class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase text-white shadow-xs"
                                    :class="dish.tag === 'BEST SELLER' ? 'bg-red-600' : (dish.tag === 'MỚI' ? 'bg-amber-500' : 'bg-emerald-600')"
                                    x-text="dish.tag"
                                >BEST SELLER</span>
                            </div>

                            <!-- Rating -->
                            <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur-xs px-2.5 py-0.5 rounded-full text-xs font-bold text-gray-800 flex items-center gap-1 shadow-xs border border-gray-100">
                                <span class="text-amber-400">⭐</span>
                                <span x-text="dish.rating">4.9</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-1.5 cursor-pointer" @click="openCustomize(dish)">
                                <h4 class="font-black text-base text-gray-900 group-hover:text-red-600 transition-colors" x-text="dish.name">
                                    Cơm Gà Sốt Cay
                                </h4>
                                <p class="text-xs text-gray-500 leading-relaxed line-clamp-2" x-text="dish.description">
                                    Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.
                                </p>
                            </div>

                            <!-- Card Footer: Price & Add Button -->
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-base sm:text-lg font-black text-red-600" x-text="formatCurrency(dish.price)">
                                    49.000đ
                                </span>
                                <button 
                                    @click="openCustomize(dish)" 
                                    type="button"
                                    class="px-4 py-1.5 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs tracking-wide shadow-xs transition-all duration-200 active:scale-95 flex items-center gap-1"
                                >
                                    <span>+ Chọn món</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

        </div>

    </div>
</div>
@endsection
