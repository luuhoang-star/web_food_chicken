<!-- STICKY BOTTOM ACTION BAR (FOR MOBILE & INSTANT ORDER) -->
<div class="fixed bottom-0 left-0 right-0 z-30 p-3 bg-white/95 backdrop-blur-md border-t border-orange-200/80 shadow-2xl md:hidden">
    <div class="flex items-center gap-3">
        <button 
            @click="isCartOpen = true" 
            type="button"
            class="relative px-4 py-3 rounded-full border border-gray-300 text-gray-700 bg-white font-bold text-xs flex items-center gap-2 cursor-pointer"
        >
            <span>🛒</span>
            <span x-text="totalItemsCount > 0 ? totalItemsCount + ' món' : 'Giỏ'"></span>
        </button>
        <a 
            href="{{ route('menu') }}" 
            class="flex-1 py-3 px-6 rounded-full bg-gradient-to-r from-red-600 to-red-500 text-white font-extrabold text-sm tracking-wide shadow-lg red-glow text-center flex items-center justify-center gap-2 active:scale-95"
        >
            <span>🍗 ĐẶT MÓN NGAY</span>
            <span x-show="totalPrice > 0" x-text="'(' + formatCurrency(totalPrice) + ')'"></span>
        </a>
    </div>
</div>
