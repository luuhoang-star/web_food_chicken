<!-- TOP NOTIFICATION BAR -->
<div class="bg-gradient-to-r from-red-600 to-amber-600 text-white text-xs font-semibold py-1.5 px-4 text-center tracking-wide flex items-center justify-center gap-2">
    <span class="inline-block animate-pulse">🔥</span>
    <span>{{ $settings['top_notification'] ?? 'Ưu đãi hôm nay: Freeship bán kính 3km cho đơn hàng từ 100k!' }}</span>
    <span class="hidden md:inline">• Hotline đặt món: <strong>{{ $settings['hotline'] ?? '0988.868.GAO' }}</strong></span>
</div>

<!-- MAIN NAVBAR -->
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-orange-100/80 transition-all duration-300 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
        
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-red-600 via-orange-600 to-amber-500 flex items-center justify-center text-white shadow-md group-hover:scale-105 transition-transform duration-200">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.87-3.13-7-7-7zm-1 18h2v2h-2v-2z"/>
                    <circle cx="12" cy="9" r="2.5" fill="#fff" opacity="0.3"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="text-2xl font-black tracking-tight text-gray-900 leading-none">{{ $settings['site_name'] ?? 'GAO' }}</span>
                    <span class="text-[10px] uppercase font-extrabold px-1.5 py-0.5 bg-red-100 text-red-700 rounded-sm">{{ $settings['location_short'] ?? 'Hà Nội' }}</span>
                </div>
                <span class="text-[11px] font-bold text-gray-400 tracking-wider block mt-0.5">{{ $settings['site_tagline'] ?? 'GÀ SỐT & CƠM' }}</span>
            </div>
        </a>

        <!-- Desktop Navigation Links (Clean & Focused) -->
        <nav class="hidden md:flex items-center gap-8 text-[15px] font-semibold">
            <a 
                href="{{ route('home') }}" 
                class="py-1 transition-colors {{ request()->routeIs('home') ? 'text-red-600 border-b-2 border-red-600 font-bold' : 'text-gray-600 hover:text-red-600' }}"
            >
                Trang chủ
            </a>

            <a 
                href="{{ route('menu') }}" 
                class="py-1 transition-colors flex items-center gap-1.5 {{ request()->routeIs('menu') ? 'text-red-600 border-b-2 border-red-600 font-bold' : 'text-gray-600 hover:text-red-600' }}"
            >
                <span>🍗 Thực Đơn Đặt Món</span>
            </a>

            <a 
                href="{{ route('quality') }}" 
                class="py-1 transition-colors {{ request()->routeIs('quality') ? 'text-red-600 border-b-2 border-red-600 font-bold' : 'text-gray-600 hover:text-red-600' }}"
            >
                Cam Kết Chất Lượng
            </a>

            <a 
                href="{{ route('order.tracking') }}" 
                class="py-1 transition-colors flex items-center gap-1 {{ request()->routeIs('order.tracking') ? 'text-red-600 border-b-2 border-red-600 font-bold' : 'text-gray-600 hover:text-red-600' }}"
            >
                <span>🛵</span>
                <span>Tra Cứu Đơn</span>
            </a>
        </nav>

        <!-- Right Controls (Location, Cart, Order Button) -->
        <div class="flex items-center gap-3">
            <!-- Location badge -->
            <div class="hidden lg:flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-gray-200 bg-gray-50/80 text-xs font-medium text-gray-700 shadow-xs">
                <span class="text-red-500 text-sm">📍</span>
                <span>{{ $settings['location_badge'] ?? 'Hà Nội (3–5km)' }}</span>
            </div>

            <!-- Shopping Cart Icon with Badge -->
            <button 
                @click="isCartOpen = true" 
                type="button"
                class="relative p-2.5 rounded-full border border-gray-200 text-gray-700 hover:border-red-400 hover:text-red-600 transition-all bg-white shadow-xs focus:outline-none cursor-pointer"
                aria-label="Giỏ hàng"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span 
                    x-text="totalItemsCount" 
                    class="absolute -top-1 -right-1 bg-red-600 text-white font-extrabold text-[11px] w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-xs animate-bounce"
                    x-show="totalItemsCount > 0"
                ></span>
                <span 
                    x-show="totalItemsCount === 0" 
                    class="absolute -top-1 -right-1 bg-gray-400 text-white font-bold text-[10px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-white"
                >0</span>
            </button>

            <!-- Order CTA Button -->
            <a 
                href="{{ $settings['header_cta_url'] ?? route('menu') }}" 
                class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-sm font-bold shadow-md red-glow transition-all duration-200 active:scale-95 cursor-pointer"
            >
                <span class="text-base">🍗</span>
                <span>{{ $settings['header_cta_text'] ?? 'Đặt món' }}</span>
            </a>
        </div>
    </div>
</header>
