<!-- SECTION: CHỌN SỐT CỦA BẠN (INTERACTIVE SAUCE SELECTOR) -->
<section id="sauces" class="py-16 lg:py-20 bg-white border-b border-orange-100/60">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center space-y-2 mb-12">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-gray-900 uppercase">
                CHỌN SỐT CỦA BẠN
            </h2>
            <p class="text-gray-500 text-base font-semibold italic">
                Gà giòn. Đậm chuẩn vị
            </p>
        </div>

        <div class="bg-[#FAF6F0] rounded-3xl p-6 sm:p-10 border border-orange-100 shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <div class="lg:col-span-6">
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-lg border-2 border-white bg-gray-900">
                        <img 
                            :src="currentSauce.image" 
                            :alt="currentSauce.name" 
                            class="w-full h-full object-cover transition-opacity duration-300"
                        />
                        <div class="absolute top-4 left-4 bg-black/70 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                            <span x-text="currentSauce.icon">🌶️</span>
                            <span x-text="currentSauce.name">Sốt Cay Hàn</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6 space-y-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                            🔥 Bán chạy nhất
                        </span>
                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold" x-text="currentSauce.tag">
                            🌶️ Vị cay đặc trưng
                        </span>
                    </div>

                    <div>
                        <h3 class="text-3xl sm:text-4xl font-black text-red-600 tracking-tight" x-text="currentSauce.name">
                            Sốt Cay Hàn
                        </h3>
                        <p class="text-base font-bold text-gray-700 mt-1" x-text="currentSauce.subtitle">
                            Cay nhẹ, ngọt hậu, thơm nồng ớt Gochujang Hàn Quốc.
                        </p>
                    </div>

                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed" x-text="currentSauce.description">
                        Là sốt đặc trưng làm nên thương hiệu GAO. Gà giòn tan quyện cùng nước sốt sánh mịn óng ả, phủ đều từng thớ thịt.
                    </p>

                    <!-- Sauce Switcher Grid -->
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <template x-for="(sauce, index) in sauceList" :key="sauce.id">
                            <button 
                                @click="selectSauce(sauce)" 
                                type="button"
                                class="p-3.5 rounded-xl text-left border-2 transition-all flex items-start gap-2.5"
                                :class="selectedSauceId === sauce.id ? 'border-red-600 bg-red-50/80 shadow-xs' : 'border-gray-200 bg-white hover:border-gray-300'"
                            >
                                <span class="text-xl" x-text="sauce.icon">🌶️</span>
                                <div>
                                    <div class="text-sm font-bold text-gray-900" x-text="sauce.name"></div>
                                    <div class="text-[11px] font-semibold text-gray-500" x-text="sauce.shortDesc"></div>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Action Button: Opens Customization Modal with current selected sauce -->
                    <div class="pt-2">
                        <button 
                            @click="openCustomizeFromSauce(currentSauce)" 
                            type="button"
                            class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm tracking-wide shadow-md red-glow transition-all active:scale-95 flex items-center justify-center gap-2"
                        >
                            <span>CHỌN VỊ NÀY</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>
