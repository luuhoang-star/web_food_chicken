@props(['sauce'])

<a 
    href="{{ route('menu', ['q' => $sauce->name]) }}" 
    class="group bg-[#FAF6F0] hover:bg-white rounded-2xl p-5 border border-orange-100/80 hover:border-red-400 hover:shadow-xl transition-all duration-300 flex flex-col justify-between h-full"
>
    <div>
        <!-- Thumbnail -->
        <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-4 bg-gray-900 shadow-sm">
            <img 
                src="{{ $sauce->image }}" 
                alt="{{ $sauce->name }}" 
                loading="lazy"
                class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500"
            />
            <div class="absolute top-2.5 left-2.5 bg-black/70 backdrop-blur-xs text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                <span>{{ $sauce->icon }}</span>
                <span>{{ $sauce->name }}</span>
            </div>
        </div>

        <!-- Title & Tag -->
        <div class="space-y-1.5 min-h-[95px]">
            <h3 class="font-black text-lg text-gray-900 group-hover:text-red-600 transition-colors">
                {{ $sauce->name }}
            </h3>
            <p class="text-xs font-bold text-amber-700 line-clamp-1">
                {{ $sauce->subtitle }}
            </p>
            <p class="text-xs text-gray-500 leading-relaxed line-clamp-2 pt-0.5">
                {{ $sauce->description }}
            </p>
        </div>
    </div>

    <div class="mt-4 pt-3 border-t border-orange-100 flex items-center justify-between text-xs font-extrabold text-red-600">
        <span>Xem các món dùng sốt này</span>
        <span class="group-hover:translate-x-1 transition-transform">→</span>
    </div>
</a>
