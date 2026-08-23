<!-- SECTION: MÓN ĐƯỢC GỌI NHIỀU (POPULAR ITEMS) -->
<section id="popular" class="py-16 lg:py-20 bg-[#FAF6F0] border-b border-orange-100/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <x-section-heading 
            badge="MÓN ĐƯỢC GỌI NHIỀU"
            badgeIcon="🔥"
            title="Thực Đơn Đậm Vị Được Yêu Thích Nhất"
            subtitle="Những món gà sốt và cơm gà được đặt nhiều nhất mỗi ngày tại GAO"
        />

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <template x-for="dish in popularItems" :key="dish.id">
                @include('partials.dish-card')
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
