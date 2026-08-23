<!-- SECTION: MÓN ĐƯỢC GỌI NHIỀU (POPULAR ITEMS) -->
<section id="popular" class="py-16 lg:py-20 bg-[#FAF6F0] border-b border-orange-100/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center space-y-2 mb-12">
            <div class="inline-flex items-center gap-2 text-red-600 font-extrabold text-sm uppercase tracking-widest">
                <span>🔥</span>
                <span>MÓN ĐƯỢC GỌI NHIỀU</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                Thực Đơn Đậm Vị Được Yêu Thích Nhất
            </h2>
            <p class="text-gray-500 text-sm sm:text-base font-medium max-w-xl mx-auto">
                Những món gà sốt và cơm gà được đặt nhiều nhất mỗi ngày tại GAO
            </p>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <template x-for="item in popularItems" :key="item.id">
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                    
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 cursor-pointer" @click="openCustomize(item)">
                        <img 
                            :src="item.image" 
                            :alt="item.name" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute top-3 left-3" x-show="item.tag">
                            <span 
                                class="px-2.5 py-1 rounded-md text-[11px] font-extrabold uppercase text-white shadow-xs"
                                :class="item.tag === 'BEST SELLER' ? 'bg-red-600' : 'bg-emerald-600'"
                                x-text="item.tag"
                            >BEST SELLER</span>
                        </div>
                        <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-xs px-2 py-0.5 rounded-full text-xs font-bold text-gray-800 flex items-center gap-1 shadow-xs">
                            <span class="text-amber-400">⭐</span>
                            <span x-text="item.rating">4.9</span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2 cursor-pointer" @click="openCustomize(item)">
                            <h3 class="font-extrabold text-base text-gray-900 group-hover:text-red-600 transition-colors" x-text="item.name">
                                Cơm Gà Sốt Cay
                            </h3>
                            <p class="text-xs text-gray-500 leading-relaxed line-clamp-2" x-text="item.description">
                                Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.
                            </p>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-lg font-black text-red-600" x-text="formatCurrency(item.price)">49.000đ</span>
                            </div>
                            <button 
                                @click="openCustomize(item)" 
                                type="button"
                                class="px-4 py-1.5 rounded-full bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs tracking-wide border border-red-200 transition-all duration-200 active:scale-95 flex items-center gap-1 cursor-pointer"
                            >
                                <span>+ Thêm</span>
                            </button>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <div class="mt-12 text-center">
            <a 
                href="{{ route('menu') }}"
                class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white font-extrabold text-sm tracking-wide transition-all duration-200 shadow-xs"
            >
                <span>XEM TOÀN BỘ MENU</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

    </div>
</section>
