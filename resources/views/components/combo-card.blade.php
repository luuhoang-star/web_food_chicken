@props([
    'combo',
    'index' => 0,
])

@php
    $isBestSeller = $combo->tag === 'BEST SELLER' || $index === 1;
@endphp

<div class="{{ $isBestSeller ? 'bg-white rounded-3xl overflow-hidden border-2 border-red-600 shadow-xl flex flex-col justify-between transform lg:-translate-y-2 relative' : 'bg-[#FAF6F0] rounded-3xl overflow-hidden border border-gray-200 shadow-sm flex flex-col justify-between transition-all hover:shadow-lg' }}">
    
    @if($isBestSeller)
        <div class="bg-red-600 text-white text-center py-2 text-xs font-black uppercase tracking-wider">
            🔥 BÁN CHẠY NHẤT • TIẾT KIỆM ĐẾN 50K
        </div>
    @endif

    <div>
        <div class="relative aspect-[16/10] overflow-hidden bg-gray-200 cursor-pointer" @click="addToCartDirect({{ json_encode([
            'id' => 'combo-' . $combo->id,
            'name' => $combo->name,
            'price' => (float)$combo->price,
            'image' => $combo->image
        ]) }})">
            <img src="{{ $combo->image }}" alt="{{ $combo->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            @if($combo->tag)
                <span class="absolute top-3 right-3 {{ $combo->tag === 'BEST SELLER' ? 'bg-red-600' : 'bg-emerald-600' }} text-white text-[11px] font-extrabold px-2.5 py-1 rounded-md uppercase shadow-xs">{{ $combo->tag }}</span>
            @endif
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase">{{ $combo->name }}</h3>
                <p class="text-xs font-bold {{ $isBestSeller ? 'text-red-600' : 'text-gray-500' }}">{{ $combo->subtag ?? 'Combo siêu tiết kiệm' }}</p>
            </div>
            
            <p class="text-xs font-semibold text-gray-600 leading-relaxed">
                {{ $combo->description }}
            </p>
        </div>
    </div>

    <div class="p-6 pt-0 flex items-center justify-between border-t {{ $isBestSeller ? 'border-gray-100' : 'border-gray-200/60' }} mt-4">
        <div>
            @if($combo->original_price)
                <span class="text-xs text-gray-400 line-through block">{{ number_format($combo->original_price, 0, ',', '.') }}đ</span>
            @endif
            <span class="{{ $isBestSeller ? 'text-2xl font-black text-red-600' : 'text-xl font-black text-red-600' }}">
                {{ number_format($combo->price, 0, ',', '.') }}đ
            </span>
        </div>
        <button 
            @click="addToCartDirect({{ json_encode([
                'id' => 'combo-' . $combo->id,
                'name' => $combo->name,
                'price' => (float)$combo->price,
                'image' => $combo->image
            ]) }})"
            type="button" 
            class="px-6 py-2.5 rounded-full {{ $isBestSeller ? 'bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold shadow-md red-glow' : 'bg-red-600 hover:bg-red-700 text-white font-bold shadow-xs' }} text-xs tracking-wide transition-all active:scale-95 cursor-pointer"
        >
            Chọn combo
        </button>
    </div>

</div>
