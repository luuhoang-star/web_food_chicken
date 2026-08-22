<!-- VIEW: THỰC ĐƠN ĐẶT MÓN (EXACT MATCH SCREENSHOT 4) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Top Back Button -->
    <div class="mb-6">
        <button 
            @click="switchView('home')" 
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 hover:bg-red-100 text-red-600 text-xs font-extrabold border border-red-200 transition-colors"
        >
            <span>←</span>
            <span>Về trang chủ</span>
        </button>
    </div>

    <!-- Page Title -->
    <div class="text-center space-y-2 mb-8">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 tracking-tight uppercase">
            THỰC ĐƠN ĐẶT MÓN
        </h1>
        <p class="text-gray-500 text-sm sm:text-base font-medium max-w-xl mx-auto">
            Chọn món yêu thích, tuỳ chỉnh sốt & độ cay theo sở thích của bạn.
        </p>
    </div>

    <!-- Category Tabs Pill Filter -->
    <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mb-6">
        <template x-for="cat in categories" :key="cat.id">
            <button 
                @click="activeCategory = cat.id"
                type="button"
                class="px-4 sm:px-5 py-2.5 rounded-full text-xs sm:text-sm font-black transition-all flex items-center gap-1.5 shadow-xs"
                :class="activeCategory === cat.id ? 'bg-red-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'"
            >
                <span x-text="cat.icon">✨</span>
                <span x-text="cat.name">Tất Cả</span>
                <span 
                    class="text-[11px] px-1.5 py-0.2 rounded-full"
                    :class="activeCategory === cat.id ? 'bg-red-700/80 text-white' : 'bg-gray-100 text-gray-500'"
                    x-text="cat.count"
                >11</span>
            </button>
        </template>
    </div>

    <!-- Search Bar -->
    <div class="max-w-md mx-auto mb-10">
        <div class="relative">
            <input 
                type="text" 
                x-model="searchQuery"
                placeholder="🔍  Tìm tên món ăn..."
                class="w-full px-5 py-3 rounded-full border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 shadow-xs placeholder:text-gray-400"
            >
            <button 
                x-show="searchQuery" 
                @click="searchQuery = ''"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs font-bold"
            >✕</button>
        </div>
    </div>

    <!-- Menu Grid: 4 Columns on Desktop matching Screenshot -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <template x-for="dish in filteredMenuItems" :key="dish.id">
            <div class="bg-white rounded-2xl overflow-hidden border border-gray-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                
                <!-- Top Thumbnail with Tag & Rating -->
                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 cursor-pointer" @click="openCustomize(dish)">
                    <img 
                        :src="dish.image" 
                        :alt="dish.name" 
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                    <!-- Tag / Badge -->
                    <div class="absolute top-3 left-3" x-show="dish.tag">
                        <span 
                            class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase text-white shadow-xs"
                            :class="dish.tag === 'BEST SELLER' ? 'bg-red-600' : (dish.tag === 'MỚI' ? 'bg-amber-500' : 'bg-emerald-600')"
                            x-text="dish.tag"
                        >BEST SELLER</span>
                    </div>

                    <!-- Rating Star Badge -->
                    <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur-xs px-2.5 py-0.5 rounded-full text-xs font-bold text-gray-800 flex items-center gap-1 shadow-xs border border-gray-100">
                        <span class="text-amber-400">⭐</span>
                        <span x-text="dish.rating">4.9</span>
                    </div>
                </div>

                <!-- Card Content Body -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div class="space-y-1.5 cursor-pointer" @click="openCustomize(dish)">
                        <h3 class="font-black text-base text-gray-900 group-hover:text-red-600 transition-colors" x-text="dish.name">
                            Cơm Gà Sốt Cay
                        </h3>
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
                            class="px-4 py-1.5 rounded-full bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs tracking-wide border border-red-200 transition-all duration-200 active:scale-95 flex items-center gap-1"
                        >
                            <span>+ Thêm</span>
                        </button>
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- Empty Search State -->
    <div x-show="filteredMenuItems.length === 0" class="text-center py-16 bg-white rounded-3xl p-8 border border-gray-200 max-w-md mx-auto">
        <div class="text-4xl mb-3">🔍</div>
        <h3 class="text-base font-bold text-gray-800">Không tìm thấy món ăn phù hợp</h3>
        <p class="text-xs text-gray-500 mt-1">Vui lòng thử tìm với từ khoá khác hoặc chọn lại danh mục.</p>
        <button 
            @click="searchQuery = ''; activeCategory = 'all'" 
            class="mt-4 px-6 py-2 rounded-full bg-red-600 text-white font-bold text-xs"
        >Xem tất cả món</button>
    </div>

</div>
