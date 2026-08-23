<div class="bg-white rounded-2xl overflow-hidden border border-gray-200/80 shadow-xs hover:shadow-lg hover:border-red-200 transition-all duration-300 flex flex-col justify-between group">
    
    <!-- Top Thumbnail with Tag & Rating -->
    <div class="relative aspect-[16/11] overflow-hidden bg-gray-100 cursor-pointer" @click="openCustomize(dish)">
        <img 
            :src="dish.image" 
            :alt="dish.name" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />
        <!-- Tag / Badge -->
        <div class="absolute top-2.5 left-2.5" x-show="dish.tag">
            <span 
                class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase text-white shadow-xs"
                :class="dish.tag === 'BEST SELLER' ? 'bg-red-600' : 'bg-emerald-600'"
                x-text="dish.tag"
            >BEST SELLER</span>
        </div>

        <!-- Rating Star Badge -->
        <div class="absolute bottom-2.5 right-2.5 bg-white/95 backdrop-blur-xs px-2 py-0.5 rounded-full text-[11px] font-bold text-gray-800 flex items-center gap-1 shadow-xs border border-gray-100">
            <span class="text-amber-400">⭐</span>
            <span x-text="dish.rating">4.9</span>
        </div>
    </div>

    <!-- Card Content Body -->
    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
        <div class="space-y-1">
            
            <!-- Badges Phân Biệt Sốt / Nhãn Món Chuẩn Xác -->
            <div>
                <!-- Trường hợp Combo / Món chọn sốt: Sốt Tự Chọn Vị -->
                <div x-show="dish.category === 'combo' || dish.sauce_selection === 'required'" class="flex items-center gap-1">
                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2 py-0.5 rounded-md border text-amber-700 bg-amber-50 border-amber-200">
                        <span>✨</span>
                        <span>Sốt: Tự chọn vị</span>
                    </span>
                </div>

                <!-- Trường hợp Món cố định sốt: Hiện vị sốt chuẩn -->
                <div x-show="dish.sauce && dish.category !== 'combo' && !['drink', 'side'].includes(dish.category)" class="flex items-center gap-1">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md border text-red-700 bg-red-50 border-red-100">
                        <span>🌶️</span>
                        <span x-text="dish.sauce">Sốt Cay Hàn</span>
                    </span>
                </div>
            </div>

            <!-- Product Title -->
            <h3 
                @click="openCustomize(dish)"
                class="font-black text-sm sm:text-base text-gray-900 group-hover:text-red-600 transition-colors leading-tight line-clamp-1 cursor-pointer pt-0.5" 
                x-text="dish.name"
            >
                Cơm Gà
            </h3>

            <!-- Description -->
            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed" x-text="dish.description">
                Đùi gà chiên giòn rụm phủ sốt đậm đà, ăn kèm cơm dẻo và dưa chua thanh mát.
            </p>
        </div>

        <!-- Bottom Card: Price & Add Button -->
        <div class="pt-2.5 border-t border-gray-100 flex items-center justify-between">
            <div>
                <span class="text-base sm:text-lg font-black text-red-600" x-text="formatCurrency(dish.price)">49.000đ</span>
                <span 
                    x-show="dish.original_price" 
                    class="text-[11px] text-gray-400 line-through font-semibold block" 
                    x-text="formatCurrency(dish.original_price)"
                >59.000đ</span>
            </div>

            <button 
                @click="openCustomize(dish)"
                type="button"
                class="px-3.5 py-1.5 rounded-full bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-extrabold text-xs border border-red-200 hover:border-red-600 transition-all duration-200 active:scale-95 flex items-center gap-1 cursor-pointer shadow-xs"
            >
                <span>+ Thêm</span>
            </button>
        </div>
    </div>
</div>
