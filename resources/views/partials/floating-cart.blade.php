<!-- FLOATING CART PILL (DESKTOP & TABLET QUICK CHECKOUT TRIGGER) -->
<div 
    x-show="totalItemsCount > 0 && !isCartOpen && !openCheckoutModal" 
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-y-12 opacity-0 scale-90"
    x-transition:enter-end="translate-y-0 opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="translate-y-0 opacity-100 scale-100"
    x-transition:leave-end="translate-y-12 opacity-0 scale-90"
    class="fixed bottom-6 right-6 z-40 hidden md:block"
    x-cloak
>
    <button 
        @click="isCartOpen = true" 
        type="button"
        class="group flex items-center gap-3.5 px-5 py-3.5 rounded-full bg-gradient-to-r from-red-600 via-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-sm tracking-wide shadow-2xl red-glow border-2 border-white/20 transition-all duration-300 hover:scale-105 active:scale-95 cursor-pointer"
    >
        <!-- Icon with dynamic badge -->
        <div class="relative flex items-center justify-center">
            <span class="text-xl group-hover:rotate-12 transition-transform duration-300">🛒</span>
            <span 
                class="absolute -top-2 -right-2.5 bg-white text-red-600 text-[11px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-md" 
                x-text="totalItemsCount"
            >1</span>
        </div>

        <!-- Total Price -->
        <div class="flex items-center gap-1.5 pl-1 border-l border-white/25">
            <span x-text="formatCurrency(totalPrice)">69.000đ</span>
        </div>

        <!-- Arrow Action -->
        <span class="inline-flex items-center gap-1 text-xs font-black bg-white/20 px-3 py-1 rounded-full group-hover:bg-white group-hover:text-red-600 transition-colors">
            <span>Xem giỏ</span>
            <span class="group-hover:translate-x-0.5 transition-transform">→</span>
        </span>
    </button>
</div>
