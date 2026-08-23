<!-- MODAL: TUỲ CHỈNH MÓN ĂN (CHỌN SỐT CHO COMBO + TOPPING + GHI CHÚ) -->
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

        <!-- Modal Box -->
        <div 
            x-show="openCustomizeModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white rounded-[28px] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full max-h-[90vh] flex flex-col"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                <h3 class="text-xl font-black text-gray-900 tracking-tight" id="modal-customize-title">
                    Tuỳ chỉnh món ăn
                </h3>
                <button 
                    @click="openCustomizeModal = false" 
                    class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="px-6 py-5 overflow-y-auto space-y-5 flex-1 scrollbar-thin">
                
                <!-- Dish Header Summary -->
                <div class="flex items-start gap-4 p-3.5 rounded-2xl bg-orange-50/50 border border-orange-100/60">
                    <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-2xl overflow-hidden shrink-0 border border-gray-100 bg-white shadow-xs">
                        <img :src="customizingItem.image" :alt="customizingItem.name" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 space-y-1">
                        <h4 class="text-lg sm:text-xl font-black text-gray-900 leading-tight" x-text="customizingItem.name">
                            Gà Sốt Cay Hàn
                        </h4>
                        <div class="text-base sm:text-lg font-black text-red-600" x-text="formatCurrency(singleCustomizedPrice)">
                            45.000đ
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-2" x-text="customizingItem.description">
                            Gà giòn rụm đẫm sốt thơm lừng, da bóng bẩy ăn kèm dưa chua thanh mát.
                        </p>
                    </div>
                </div>

                <!-- 1. CHỌN VỊ SỐT (BẮT BUỘC DÀNH CHO COMBO) -->
                <div class="space-y-2.5" x-show="customizingItem.is_sauce_choice">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900 flex items-center gap-1.5">
                            <span>🌶️</span>
                            <span>1. Chọn vị sốt đặc trưng</span>
                        </h5>
                        <span class="text-[11px] font-bold text-red-600 bg-red-50 px-2.5 py-0.5 rounded-full">Bắt buộc</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <template x-for="sauce in sauceList" :key="sauce.id || sauce.slug">
                            <button 
                                @click="customizingItem.selectedSauce = sauce.name"
                                type="button"
                                class="p-3 rounded-2xl border text-left flex items-center gap-2.5 transition-all cursor-pointer select-none"
                                :class="customizingItem.selectedSauce === sauce.name 
                                    ? 'border-2 border-red-500 bg-[#FFF5F5] font-black text-gray-900 shadow-xs' 
                                    : 'border-gray-200/90 bg-white hover:border-gray-300 font-bold text-gray-800'"
                            >
                                <span class="text-lg" x-text="sauce.icon || '🌶️'">🌶️</span>
                                <span class="text-xs sm:text-sm truncate" x-text="sauce.name">Sốt Cay Hàn</span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- 2. THÊM TOPPING (Tuỳ chọn - Hiển thị 100% tên món đầy đủ, rõ ràng) -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900 flex items-center gap-1.5">
                            <span>🍳</span>
                            <span x-text="customizingItem.is_sauce_choice ? '2. Thêm Topping món' : '1. Thêm Topping món'">1. Thêm Topping món</span>
                        </h5>
                        <span class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">Tuỳ chọn</span>
                    </div>
                    <div class="space-y-2">
                        <template x-for="top in availableToppings" :key="top.id || top.name">
                            <label 
                                class="flex items-center justify-between p-3 rounded-2xl border cursor-pointer transition-all hover:border-gray-300 select-none"
                                :class="(customizingItem.selectedToppings || []).includes(top.id) 
                                    ? 'border-2 border-red-500 bg-[#FFF5F5] shadow-xs' 
                                    : 'border-gray-200/90 bg-white'"
                            >
                                <div class="flex items-center gap-3 pr-2">
                                    <input 
                                        type="checkbox" 
                                        :checked="(customizingItem.selectedToppings || []).includes(top.id)"
                                        @change="toggleTopping(top.id)"
                                        class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer shrink-0"
                                    >
                                    <span class="text-lg shrink-0" x-text="top.icon">🍳</span>
                                    <span class="text-xs sm:text-sm font-bold text-gray-900 leading-snug" x-text="top.name">Trứng Ốp La Lòng Đào</span>
                                </div>
                                <span class="text-xs sm:text-sm font-black text-red-600 shrink-0 whitespace-nowrap" x-text="'+' + formatCurrency(top.price)">+10.000đ</span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- 3. THÊM MÓN ĂN KÈM (Tuỳ chọn - Hiển thị tên đầy đủ & ảnh minh họa) -->
                <div class="space-y-2.5" x-show="availableSides && availableSides.length > 0">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900 flex items-center gap-1.5">
                            <span>🍟</span>
                            <span x-text="customizingItem.is_sauce_choice ? '3. Thêm Món Ăn Kèm' : '2. Thêm Món Ăn Kèm'">2. Thêm Món Ăn Kèm</span>
                        </h5>
                        <span class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">Tuỳ chọn</span>
                    </div>
                    <div class="space-y-2">
                        <template x-for="side in availableSides" :key="side.id">
                            <label 
                                class="flex items-center justify-between p-3 rounded-2xl border cursor-pointer transition-all hover:border-gray-300 select-none"
                                :class="(customizingItem.selectedSides || []).includes(side.id) 
                                    ? 'border-2 border-red-500 bg-[#FFF5F5] shadow-xs' 
                                    : 'border-gray-200/90 bg-white'"
                            >
                                <div class="flex items-center gap-3 pr-2">
                                    <input 
                                        type="checkbox" 
                                        :checked="(customizingItem.selectedSides || []).includes(side.id)"
                                        @change="toggleSide(side.id)"
                                        class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer shrink-0"
                                    >
                                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-gray-100 bg-gray-100 shadow-2xs">
                                        <img :src="side.image" :alt="side.name" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-xs sm:text-sm font-bold text-gray-900 leading-snug" x-text="side.name">Khoai Tây Chiên Giòn</span>
                                </div>
                                <span class="text-xs sm:text-sm font-black text-red-600 shrink-0 whitespace-nowrap" x-text="'+' + formatCurrency(side.price)">+20.000đ</span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- 4. THÊM ĐỒ UỐNG GIẢI KHÁT (Tuỳ chọn - Hiển thị tên đầy đủ & ảnh lon nước) -->
                <div class="space-y-2.5" x-show="availableDrinks && availableDrinks.length > 0">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900 flex items-center gap-1.5">
                            <span>🥤</span>
                            <span x-text="customizingItem.is_sauce_choice ? '4. Thêm Đồ Uống' : '3. Thêm Đồ Uống'">3. Thêm Đồ Uống</span>
                        </h5>
                        <span class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">Tuỳ chọn</span>
                    </div>
                    <div class="space-y-2">
                        <template x-for="drink in availableDrinks" :key="drink.id">
                            <label 
                                class="flex items-center justify-between p-3 rounded-2xl border cursor-pointer transition-all hover:border-gray-300 select-none"
                                :class="(customizingItem.selectedDrinks || []).includes(drink.id) 
                                    ? 'border-2 border-red-500 bg-[#FFF5F5] shadow-xs' 
                                    : 'border-gray-200/90 bg-white'"
                            >
                                <div class="flex items-center gap-3 pr-2">
                                    <input 
                                        type="checkbox" 
                                        :checked="(customizingItem.selectedDrinks || []).includes(drink.id)"
                                        @change="toggleDrink(drink.id)"
                                        class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer shrink-0"
                                    >
                                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-gray-100 bg-gray-100 shadow-2xs">
                                        <img :src="drink.image" :alt="drink.name" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-xs sm:text-sm font-bold text-gray-900 leading-snug" x-text="drink.name">Coca Cola (Lon 320ml)</span>
                                </div>
                                <span class="text-xs sm:text-sm font-black text-red-600 shrink-0 whitespace-nowrap" x-text="'+' + formatCurrency(drink.price)">+12.000đ</span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Ghi chú cho quán (Không bắt buộc) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-black text-gray-900">Ghi chú cho quán</h5>
                        <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2.5 py-0.5 rounded-full">Không bắt buộc</span>
                    </div>
                    <textarea 
                        x-model="customizingItem.note"
                        rows="2"
                        placeholder="Ví dụ: Cơm nhiều, ít tương ớt, để riêng..."
                        class="w-full p-3 text-xs border border-gray-200 rounded-2xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                    ></textarea>
                </div>

            </div>

            <!-- Modal Bottom Bar (Stepper + Big Red Action Button) -->
            <div class="p-5 bg-white border-t border-gray-100 flex items-center gap-3.5 shrink-0 shadow-lg">
                <!-- Quantity Controls -->
                <div class="flex items-center justify-between border border-gray-200 rounded-full bg-white px-3.5 py-2.5 w-32 shadow-xs">
                    <button 
                        @click="if (customizingItem.quantity > 1) customizingItem.quantity--" 
                        type="button"
                        class="text-gray-500 hover:text-gray-900 font-black text-sm w-6 text-center cursor-pointer select-none"
                    >-</button>
                    <span class="font-black text-base text-gray-900 w-6 text-center" x-text="customizingItem.quantity">1</span>
                    <button 
                        @click="customizingItem.quantity++" 
                        type="button"
                        class="text-gray-500 hover:text-gray-900 font-black text-sm w-6 text-center cursor-pointer select-none"
                    >+</button>
                </div>

                <!-- Big Red Action Button -->
                <button 
                    @click="confirmAddToCart()" 
                    type="button" 
                    class="flex-1 py-4 px-6 rounded-full bg-[#E5251F] hover:bg-red-700 text-white font-black text-sm sm:text-base tracking-wide shadow-lg red-glow transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer"
                >
                    <span>THÊM VÀO GIỎ</span>
                    <span>•</span>
                    <span x-text="formatCurrency(totalCustomizedPrice)">45.000đ</span>
                </button>
            </div>

        </div>
    </div>
</div>
