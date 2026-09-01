<!-- DRAWER: GIỎ HÀNG CỦA BẠN (DYNAMIC SETTINGS) -->
<div 
    x-show="isCartOpen" 
    class="fixed inset-0 z-50 overflow-hidden" 
    aria-labelledby="slide-over-title" 
    role="dialog" 
    aria-modal="true"
    x-cloak
>
    <!-- Backdrop -->
    <div 
        x-show="isCartOpen"
        x-transition:enter="ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
        @click="isCartOpen = false"
    ></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-6 sm:pl-10">
        <div 
            x-show="isCartOpen"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-screen max-w-md bg-[#FAF6F0] shadow-2xl flex flex-col"
        >
            <!-- Drawer Header -->
            <div class="p-5 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">🛒</span>
                    <h2 class="text-lg font-black text-gray-900 tracking-tight" id="slide-over-title">
                        Giỏ Hàng Của Bạn
                    </h2>
                </div>
                <button 
                    @click="isCartOpen = false" 
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Free Ship Notification Bar -->
            @php
                $freeshipLimit = (int) ($settings['freeship_threshold'] ?? 100000);
                $defaultShipping = (int) ($settings['shipping_base_fee'] ?? ($settings['shipping_fee_default'] ?? 15000));
            @endphp
            <div class="bg-white px-5 py-3 border-b border-orange-100/70 shrink-0">
                <div class="flex items-center gap-2 text-xs font-black text-gray-800">
                    <span>🎉</span>
                    <span x-show="totalPrice >= {{ $freeshipLimit }}">Bạn đã được <strong>FREE SHIP 3KM!</strong></span>
                    <span x-show="totalPrice < {{ $freeshipLimit }}">Mua thêm <strong class="text-red-600" x-text="formatCurrency({{ $freeshipLimit }} - totalPrice)"></strong> để được <strong>FREE SHIP!</strong></span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full h-1.5 bg-gray-100 rounded-full mt-2 overflow-hidden">
                    <div 
                        class="h-full bg-gradient-to-r from-red-600 to-amber-500 rounded-full transition-all duration-300"
                        :style="'width: ' + Math.min(100, Math.round((totalPrice / {{ $freeshipLimit }}) * 100)) + '%'"
                    ></div>
                </div>
            </div>

            <!-- Drawer Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-5 space-y-4">
                
                <!-- Empty State -->
                <div x-show="cartItems.length === 0" class="text-center py-16 space-y-4 bg-white rounded-3xl p-6 border border-gray-100 shadow-xs">
                    <div class="w-20 h-20 mx-auto rounded-full bg-red-50 flex items-center justify-center text-4xl">
                        🍗
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Chưa có món nào trong giỏ</h3>
                    <p class="text-xs text-gray-500 max-w-xs mx-auto">Hãy chọn ngay các món gà giòn phủ sốt thơm ngon để thưởng thức nhé!</p>
                    <div class="pt-2">
                        <a 
                            href="{{ route('menu') }}" 
                            class="px-6 py-2.5 rounded-full bg-red-600 text-white font-bold text-xs tracking-wide shadow-md inline-block"
                        >
                            Xem thực đơn ngay
                        </a>
                    </div>
                </div>

                <!-- Cart Item Cards -->
                <template x-for="(item, index) in cartItems" :key="index">
                    <div class="p-4 rounded-2xl border border-gray-200/70 bg-white shadow-xs space-y-3 relative">
                        
                        <!-- Delete Button (Top-Right X) -->
                        <button 
                            @click="removeItem(index)" 
                            class="absolute top-3.5 right-3.5 w-6 h-6 rounded-full bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-600 flex items-center justify-center transition-colors cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div class="flex items-start gap-3.5 pr-6">
                            <!-- Food / Sauce Thumbnail -->
                            <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 border border-gray-100 bg-gray-100">
                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                            </div>

                            <!-- Info & Option Badges -->
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center gap-1.5">
                                    <h4 class="font-black text-sm text-gray-900 leading-tight truncate" x-text="item.name">
                                        Cơm Gà Sốt Cay
                                    </h4>
                                    <!-- Sauce Badge -->
                                    <span x-show="item.item_type === 'sauce'" class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded-sm bg-red-100 text-red-700">
                                        Sốt lẻ
                                    </span>
                                </div>

                                <!-- Option Tags (Sauce, Toppings) for dishes -->
                                <div class="flex flex-wrap gap-1.5 pt-0.5" x-show="item.item_type !== 'sauce' && item.sauce">
                                    <span x-show="item.sauce" class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded-md" x-text="'🌶️ ' + item.sauce">
                                        🌶️ Sốt Cay Hàn
                                    </span>
                                </div>

                                <!-- Note / Sauce description -->
                                <div x-show="item.item_type === 'sauce'" class="text-[11px] text-gray-500 font-semibold">
                                    Hũ sốt chấm / trộn thêm hảo hạng
                                </div>

                                <!-- Toppings tag list -->
                                <div class="text-[11px] text-gray-500 font-semibold" x-show="item.toppings && item.toppings.length > 0">
                                    + <span x-text="item.toppings.join(', ')"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Row: Total Item Price & Quantity Selector -->
                        <div class="flex items-center justify-between pt-1">
                            <div class="text-base font-black text-red-600" x-text="formatCurrency(item.price * item.quantity)">
                                147.000đ
                            </div>
                            <div class="flex items-center gap-2.5 px-3 py-1 rounded-full border border-gray-200 bg-gray-50">
                                <button 
                                    @click="decrementItem(index)" 
                                    class="text-gray-500 hover:text-gray-900 font-bold text-xs w-4 text-center cursor-pointer"
                                >-</button>
                                <span class="font-black text-xs w-4 text-center text-gray-900" x-text="item.quantity">3</span>
                                <button 
                                    @click="incrementItem(index)" 
                                    class="text-gray-500 hover:text-gray-900 font-bold text-xs w-4 text-center cursor-pointer"
                                >+</button>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- Upsell Carousel / Strip -->
                <div class="pt-2 space-y-2.5" x-show="cartItems.length > 0">
                    <div class="text-xs font-black text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                        <span>🥤</span>
                        <span>GỢI Ý THÊM MÓN NGON:</span>
                    </div>
                    <div class="flex gap-2.5 overflow-x-auto pb-2 scrollbar-none">
                        <template x-for="up in upsellItems" :key="up.id">
                            <div class="shrink-0 bg-white border border-gray-200 rounded-full px-3.5 py-1.5 flex items-center gap-2 shadow-xs">
                                <span class="text-sm" x-text="up.icon">🥤</span>
                                <span class="text-xs font-bold text-gray-800 whitespace-nowrap" x-text="up.name">Coca Cola</span>
                                <span class="text-xs font-black text-red-600" x-text="'+' + formatCurrency(up.price)">+12.000đ</span>
                                <button 
                                    @click="addToCartDirect(up)" 
                                    class="text-[11px] font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-0.5 rounded-full cursor-pointer"
                                >+ Thêm</button>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Drawer Footer & Payment Summary -->
            <div x-show="cartItems.length > 0" class="p-6 bg-white border-t border-gray-200/80 space-y-4 shrink-0 shadow-lg">
                <div class="space-y-2 text-xs font-bold text-gray-600">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tạm tính:</span>
                        <span class="text-gray-900 font-black" x-text="formatCurrency(totalPrice)">313.000đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phí giao hàng:</span>
                        <span class="font-bold" :class="totalPrice >= {{ $freeshipLimit }} ? 'text-emerald-600' : 'text-gray-900'" x-text="totalPrice >= {{ $freeshipLimit }} ? '0đ (Freeship)' : formatCurrency({{ $defaultShipping }})">0đ (Freeship)</span>
                    </div>
                    <div class="flex justify-between items-center text-base pt-2 border-t border-dashed border-gray-200">
                        <span class="font-black text-gray-900">Tổng cộng:</span>
                        <span class="text-2xl font-black text-red-600" x-text="formatCurrency(totalPrice >= {{ $freeshipLimit }} ? totalPrice : totalPrice + {{ $defaultShipping }})">313.000đ</span>
                    </div>
                </div>

                <!-- Big Checkout Button -> Opens Checkout Modal -->
                <button 
                    @click="openCheckout()" 
                    type="button" 
                    class="w-full py-4 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-base tracking-wider uppercase shadow-xl red-glow text-center transition-all active:scale-95 cursor-pointer"
                >
                    THANH TOÁN
                </button>
            </div>

        </div>
    </div>
</div>
