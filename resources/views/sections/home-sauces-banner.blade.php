<!-- HOME SAUCES TEASER / SPOTLIGHT BANNER -->
<section class="py-14 bg-white border-b border-orange-100/60 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-black uppercase tracking-wider mb-2">
                    <span>🌶️</span>
                    <span>TINH HOA HƯƠNG VỊ</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                    4 VỊ SỐT ĐẶC TRƯNG TẠI GAO
                </h2>
                <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">
                    Sốt thủ công nguyên bản, sánh mịn thơm lừng phủ đẫm trên từng miếng gà giòn rụm.
                </p>
            </div>
            <div>
                <a 
                    href="{{ route('menu') }}" 
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-red-50 hover:bg-red-100 text-red-600 font-extrabold text-sm border border-red-200 transition-all hover:gap-3 group shadow-xs cursor-pointer"
                >
                    <span>Xem thực đơn đặt món</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                </a>
            </div>
        </div>

        <!-- 4 Sauces Grid Cards (Clicking goes to /menu?sauce=slug) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($sauces as $sauce)
            <a 
                href="{{ route('menu', ['q' => $sauce->name]) }}" 
                class="group bg-[#FAF6F0] hover:bg-white rounded-2xl p-5 border border-orange-100/80 hover:border-red-400 hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
            >
                <div>
                    <!-- Thumbnail -->
                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-4 bg-gray-900 shadow-sm">
                        <img 
                            src="{{ $sauce->image }}" 
                            alt="{{ $sauce->name }}" 
                            class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500"
                        />
                        <div class="absolute top-2.5 left-2.5 bg-black/70 backdrop-blur-xs text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                            <span>{{ $sauce->icon }}</span>
                            <span>{{ $sauce->name }}</span>
                        </div>
                    </div>

                    <!-- Title & Tag -->
                    <div class="space-y-1">
                        <h3 class="font-black text-lg text-gray-900 group-hover:text-red-600 transition-colors">
                            {{ $sauce->name }}
                        </h3>
                        <p class="text-xs font-bold text-amber-700 line-clamp-1">
                            {{ $sauce->subtitle }}
                        </p>
                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-2 pt-1">
                            {{ $sauce->description }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-orange-100 flex items-center justify-between text-xs font-extrabold text-red-600">
                    <span>Xem các món dùng sốt này</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>
            @endforeach
        </div>

    </div>
</section>
