<!-- MODAL 1: TUỲ CHỈNH MÓN ĂN (MATCHING SCREENSHOT 1 & 2) -->
<div 
    x-show="openCustomizeModal" 
    class="fixed inset-0 z-50 overflow-y-auto" 
    aria-labelledby="modal-customize-title" 
    role="dialog" 
    aria-modal="true"
    x-cloak
>
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Backdrop -->
        <div 
            x-show="openCustomizeModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
            @click="openCustomizeModal = false"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Content Box -->
        <div 
            x-show="openCustomizeModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full max-h-[90vh] flex flex-col"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                <h3 class="text-lg font-black text-gray-900" id="modal-customize-title">
                    Tuỳ chỉnh món ăn
                </h3>
                <button 
                    @click="openCustomizeModal = false" 
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="px-6 py-5 overflow-y-auto space-y-6 flex-1">
                
                <!-- Dish Header Summary -->
                <div class="flex items-start gap-4">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden shrink-0 border border-gray-100 bg-gray-100 shadow-xs">
                        <img :src="customizingItem.image" :alt="customizingItem.name" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 space-y-1">
                        <h4 class="text-xl font-black text-gray-900 leading-tight" x-text="customizingItem.name">
                            Cơm Gà Sốt Cay
                        </h4>
                        <div class="flex items-center gap-1 text-xs font-bold text-gray-500">
                            <span class="text-amber-400">⭐</span>
                            <span x-text="customizingItem.rating">4.9 (384 đánh giá)</span>
                        </div>
                        <div class="text-lg font-black text-red-600" x-text="formatCurrency(customizingItem.basePrice)">
                            49.000đ
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed pt-0.5 line-clamp-2" x-text="customizingItem.description">
                            Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.
                        </p>
                    </div>
                </div>

                <!-- 1. Chọn loại sốt (Bắt buộc) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900">1. Chọn loại sốt</h5>
                        <span class="text-[11px] font-bold text-red-600 bg-red-50 px-2.5 py-0.5 rounded-full">Bắt buộc</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <template x-for="sauce in sauceList" :key="sauce.id">
                            <button 
                                @click="customizingItem.selectedSauce = sauce.name"
                                type="button"
                                class="p-3 rounded-2xl border-2 text-left flex items-center gap-2.5 transition-all"
                                :class="customizingItem.selectedSauce === sauce.name ? 'border-red-500 bg-red-50/70 text-gray-900 shadow-xs' : 'border-gray-200/80 bg-white hover:border-gray-300 text-gray-700'"
                            >
                                <span class="text-lg" x-text="sauce.icon">🌶️</span>
                                <span class="text-xs font-black" x-text="sauce.name">Sốt Cay Hàn</span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- 2. Chọn độ cay (Tuỳ chọn) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900">2. Chọn độ cay</h5>
                        <span class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">Tuỳ chọn</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <template x-for="spice in spiceLevels" :key="spice.id">
                            <button 
                                @click="customizingItem.selectedSpiceLevel = spice.name"
                                type="button"
                                class="p-3 rounded-2xl border-2 text-left transition-all"
                                :class="customizingItem.selectedSpiceLevel === spice.name ? 'border-red-500 bg-red-50/70 shadow-xs' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                            >
                                <div class="text-xs font-black text-gray-900" x-text="spice.name">Cay nhẹ (Chuẩn vị)</div>
                                <div class="text-[10px] text-gray-500 font-semibold mt-0.5 leading-tight" x-text="spice.desc">Hơi tê tê đầu lưỡi, chuẩn vị GAO</div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- 3. Thêm Topping / Ăn kèm (Tuỳ chọn) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900">3. Thêm Topping / Ăn kèm</h5>
                        <span class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">Tuỳ chọn</span>
                    </div>
                    <div class="space-y-2">
                        <template x-for="top in availableToppings" :key="top.id">
                            <label 
                                class="flex items-center justify-between p-3 rounded-2xl border-2 cursor-pointer transition-all"
                                :class="customizingItem.selectedToppings.includes(top.id) ? 'border-red-400 bg-red-50/40 shadow-xs' : 'border-gray-200/70 bg-white hover:border-gray-300'"
                            >
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="checkbox" 
                                        :checked="customizingItem.selectedToppings.includes(top.id)"
                                        @change="toggleTopping(top.id)"
                                        class="w-4 h-4 text-red-600 rounded-md border-gray-300 focus:ring-red-500"
                                    >
                                    <span class="text-base" x-text="top.icon">🍳</span>
                                    <span class="text-xs font-bold text-gray-900" x-text="top.name">Trứng Ốp La Lòng Đào</span>
                                </div>
                                <span class="text-xs font-black text-red-600" x-text="'+' + formatCurrency(top.price)">+10.000đ</span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Ghi chú cho quán (Không bắt buộc) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900">Ghi chú cho quán</h5>
                        <span class="text-[11px] font-bold text-gray-400 bg-gray-100 px-2.5 py-0.5 rounded-full">Không bắt buộc</span>
                    </div>
                    <textarea 
                        x-model="customizingItem.note"
                        rows="2"
                        placeholder="Ví dụ: Cơm nhiều, ít tương ớt, để sốt riêng..."
                        class="w-full p-3 text-xs border border-gray-200 rounded-2xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                    ></textarea>
                </div>

            </div>

            <!-- Modal Bottom Bar (Counter + Add to Cart Button) -->
            <div class="p-4 sm:p-5 bg-white border-t border-gray-100 flex items-center gap-3 shrink-0">
                <!-- Quantity Controls -->
                <div class="flex items-center gap-2 px-3 py-2 rounded-full border border-gray-200 bg-gray-50/80">
                    <button 
                        @click="if (customizingItem.quantity > 1) customizingItem.quantity--" 
                        class="w-6 h-6 rounded-full bg-white border border-gray-200 font-bold text-xs flex items-center justify-center hover:bg-gray-100 text-gray-700"
                    >-</button>
                    <span class="font-black text-sm w-5 text-center text-gray-900" x-text="customizingItem.quantity">1</span>
                    <button 
                        @click="customizingItem.quantity++" 
                        class="w-6 h-6 rounded-full bg-white border border-gray-200 font-bold text-xs flex items-center justify-center hover:bg-gray-100 text-gray-700"
                    >+</button>
                </div>

                <!-- Add To Cart Button (Directly switches to Cart Drawer!) -->
                <button 
                    @click="confirmAddToCart()" 
                    type="button" 
                    class="flex-1 py-3.5 px-6 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm tracking-wide shadow-lg red-glow transition-all active:scale-95 flex items-center justify-center gap-2"
                >
                    <span>THÊM VÀO GIỎ</span>
                    <span>•</span>
                    <span x-text="formatCurrency(totalCustomizedPrice)">49.000đ</span>
                </button>
            </div>

        </div>
    </div>
</div>
