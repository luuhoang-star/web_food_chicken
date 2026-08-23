@extends('layouts.app')

@section('title', $sauce->name . ' - Vị Sốt Đậm Đà | GAO - Gà Sốt & Cơm Hà Nội')

@section('content')
<div class="py-8 sm:py-12 bg-[#FAF6F0]" x-data="{ qty: 1 }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between gap-4 mb-8">
            <nav class="flex items-center gap-2 text-xs font-bold text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('sauces.index') }}" class="hover:text-red-600 transition-colors">Chọn Vị Sốt</a>
                <span>/</span>
                <span class="text-red-600 font-extrabold">{{ $sauce->name }}</span>
            </nav>
            <a 
                href="{{ route('sauces.index') }}" 
                class="inline-flex items-center gap-1.5 text-xs font-black text-gray-600 hover:text-red-600"
            >
                <span>← Xem tất cả vị sốt</span>
            </a>
        </div>

        <!-- Sauce Detail Standalone Box (No chicken list below) -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-orange-200/80 shadow-xl mb-12">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                
                <!-- Sauce Image -->
                <div class="md:col-span-5">
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-lg border-2 border-orange-100 bg-gray-900 group">
                        <img 
                            src="{{ $sauce->image }}" 
                            alt="{{ $sauce->name }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute top-3 left-3 bg-black/70 backdrop-blur-xs text-white text-xs font-extrabold px-3 py-1 rounded-full flex items-center gap-1.5">
                            <span>{{ $sauce->icon }}</span>
                            <span>{{ $sauce->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sauce Info & Add to Cart -->
                <div class="md:col-span-7 space-y-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-black uppercase mb-2">
                            <span>{{ $sauce->icon }}</span>
                            <span>{{ $sauce->tag ?? 'Vị sốt độc quyền' }}</span>
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                            {{ $sauce->name }}
                        </h1>

                        <p class="text-sm sm:text-base font-bold text-amber-700 mt-1">
                            {{ $sauce->subtitle }}
                        </p>

                        <p class="text-gray-600 text-sm leading-relaxed mt-3">
                            {{ $sauce->description }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-red-600">
                            {{ number_format($sauce->price, 0, ',', '.') }}đ
                        </span>
                        <span class="text-xs font-bold text-gray-400">/ phần sốt thêm</span>
                    </div>

                    <!-- Quantity & Add to Cart Action -->
                    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        <!-- Quantity Counter -->
                        <div class="flex items-center border-2 border-gray-200 rounded-full bg-white px-3 py-1.5 shadow-xs w-fit">
                            <button 
                                @click="if(qty > 1) qty--" 
                                type="button" 
                                class="w-8 h-8 rounded-full hover:bg-gray-100 font-black text-gray-700 flex items-center justify-center transition-colors"
                            >-</button>
                            <span class="w-10 text-center font-black text-sm text-gray-900" x-text="qty">1</span>
                            <button 
                                @click="qty++" 
                                type="button" 
                                class="w-8 h-8 rounded-full hover:bg-gray-100 font-black text-gray-700 flex items-center justify-center transition-colors"
                            >+</button>
                        </div>

                        <!-- Add Sauce to Cart Button -->
                        <button 
                            @click="addSauceToCart({{ json_encode([
                                'id' => $sauce->id,
                                'slug' => $sauce->slug,
                                'name' => $sauce->name,
                                'price' => (float)$sauce->price,
                                'image' => $sauce->image,
                                'icon' => $sauce->icon
                            ]) }}, qty)" 
                            type="button"
                            class="flex-1 py-4 px-8 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-sm tracking-wide shadow-md red-glow transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span>🛒 THÊM SỐT VÀO GIỎ</span>
                            <span x-text="'(' + formatCurrency({{ (int)$sauce->price }} * qty) + ')'"></span>
                        </button>
                    </div>

                    <!-- Cross link to menu filtered by this sauce -->
                    <div class="pt-3 border-t border-orange-100">
                        <a 
                            href="{{ route('menu', ['sauce' => $sauce->slug]) }}" 
                            class="inline-flex items-center gap-2 text-xs font-black text-red-600 hover:text-red-700 hover:underline"
                        >
                            <span>🍗 Bạn muốn tìm các món ăn chế biến với {{ $sauce->name }}? Xem trên Menu</span>
                            <span>→</span>
                        </a>
                    </div>

                </div>

            </div>
        </div>

        <!-- Quick Switch Other Sauces Bar -->
        <div class="bg-[#FAF6F0] rounded-2xl p-6 border border-orange-200">
            <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-4 text-center">
                CÁC VỊ SỐT ĐẶC TRƯNG KHÁC CỦA GAO
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach($sauces as $otherSauce)
                <a 
                    href="{{ route('sauces.show', $otherSauce->slug) }}" 
                    class="p-3 rounded-xl border text-center transition-all {{ $otherSauce->id === $sauce->id ? 'border-red-600 bg-red-50/80 font-black text-red-700 ring-2 ring-red-500/20' : 'border-gray-200 bg-white hover:border-gray-300 text-gray-700' }}"
                >
                    <span class="text-lg block">{{ $otherSauce->icon }}</span>
                    <span class="text-xs font-bold block mt-1">{{ $otherSauce->name }}</span>
                    <span class="text-[10px] text-red-600 font-extrabold block mt-0.5">{{ number_format($otherSauce->price, 0, ',', '.') }}đ</span>
                </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
