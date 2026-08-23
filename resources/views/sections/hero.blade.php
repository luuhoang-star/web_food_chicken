<!-- HERO SECTION -->
<section id="hero" class="relative overflow-hidden py-12 lg:py-16 hero-glow border-b border-orange-100/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
            
            <!-- Left Column: Copywriting & CTA -->
            <div class="lg:col-span-6 space-y-6 text-center lg:text-left">
                @if($hero?->badge)
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-50 border border-red-200/80 text-red-600 font-bold text-xs tracking-wider uppercase shadow-xs">
                        <span class="inline-block text-sm">✦</span>
                        <span>{{ $hero->badge }}</span>
                    </div>
                @endif

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black tracking-tight text-gray-900 leading-[1.05]">
                    {{ $hero->title ?? 'GÀ GIÒN.' }}<br>
                    <span class="text-red-600 inline-block drop-shadow-xs">{{ $hero->title_highlight ?? 'SỐT ĐẬM.' }}</span>
                </h1>

                <p class="text-gray-600 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed">
                    {{ $hero->subtitle ?? 'Gà nóng giòn, phủ sốt nguyên bản. Giao tận nơi tại Hà Nội trong 25–40 phút.' }}
                </p>

                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                    <a 
                        href="{{ $hero->cta_primary_url ?? route('menu') }}" 
                        class="px-8 py-4 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm sm:text-base tracking-wide uppercase shadow-lg red-glow transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 inline-flex items-center gap-2"
                    >
                        <span>{{ $hero->cta_primary_text ?? '🍗 ĐẶT MÓN NGAY' }}</span>
                    </a>
                    @if($hero?->cta_secondary_text)
                        <a 
                            href="{{ $hero->cta_secondary_url ?? route('menu') }}"
                            class="px-7 py-4 rounded-full bg-white hover:bg-gray-50 text-gray-800 font-bold text-sm sm:text-base tracking-wide border border-gray-200 shadow-sm transition-all duration-200 hover:border-red-300 inline-flex items-center gap-2"
                        >
                            <span>{{ $hero->cta_secondary_text }}</span>
                        </a>
                    @endif
                </div>

                <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 sm:gap-8 text-xs sm:text-sm font-bold text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="text-red-500 text-base">⚡</span>
                        <span>{{ $hero->delivery_time ?? 'Giao 25–40p' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-amber-500 text-base">🔥</span>
                        <span>{{ $hero->hot_status ?? 'Luôn nóng giòn' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-yellow-500 text-base">⭐</span>
                        <span>{{ $hero->rating ?? 'Đánh giá 4.9/5' }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Hero Visual Graphic -->
            <div class="lg:col-span-6 relative flex justify-center">
                <div class="relative w-full max-w-lg cursor-pointer group" @click="openCustomize(allMenuItems[0])">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-red-500/20 to-amber-400/20 rounded-full blur-2xl opacity-70"></div>
                    
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white aspect-[4/3] bg-gray-900">
                        <img 
                            src="{{ $hero->image ?? 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=1000&q=85' }}" 
                            alt="{{ $hero->title ?? 'Gà Giòn Sốt Đậm GAO' }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                        <div class="absolute bottom-5 right-5 bg-white/95 backdrop-blur-md py-2 px-4 rounded-full shadow-lg border border-red-100 flex items-center gap-2">
                            <span class="text-[11px] font-bold uppercase text-gray-500 tracking-wider">CHỈ TỪ</span>
                            <span class="text-lg font-black text-red-600">{{ number_format($hero->price ?? 49000, 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    @if($hero?->floating_badge)
                        <div class="absolute -top-3 -left-3 bg-red-600 text-white font-extrabold text-xs px-3.5 py-1.5 rounded-full shadow-md flex items-center gap-1.5 hover:scale-105 transition-transform">
                            <span>🔥</span>
                            <span>{{ $hero->floating_badge }}</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
