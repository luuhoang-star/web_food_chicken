@extends('layouts.app')

@section('title', 'Chọn Vị Sốt | GAO - Gà Sốt & Cơm Hà Nội')

@section('content')
<div class="py-8 sm:py-12 bg-[#FAF6F0]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between gap-4 mb-8">
            <nav class="flex items-center gap-2 text-xs font-bold text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Trang chủ</a>
                <span>/</span>
                <span class="text-red-600 font-extrabold">Chọn Vị Sốt</span>
            </nav>
            <a 
                href="{{ route('menu') }}" 
                class="inline-flex items-center gap-1.5 text-xs font-black text-gray-700 hover:text-red-600 bg-white px-4 py-2 rounded-full border border-gray-200 shadow-xs hover:border-red-300 transition-colors"
            >
                <span>Xem thực đơn món ăn</span>
                <span>→</span>
            </a>
        </div>

        <!-- Page Header -->
        <div class="text-center max-w-2xl mx-auto space-y-2 mb-12">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-black uppercase tracking-wider">
                <span>🌶️</span>
                <span>SỐT THỦ CÔNG ĐẬM VỊ</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 tracking-tight uppercase">
                CHỌN VỊ SỐT
            </h1>
            <p class="text-gray-600 text-sm sm:text-base font-medium leading-relaxed">
                Khám phá các loại sốt đặc trưng của GÀO và thêm ngay hương vị yêu thích vào đơn hàng.
            </p>
        </div>

        <!-- Sauces Grid (4 Sauces as Standalone Add-on Items) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            @foreach($sauces as $sauce)
            <div 
                class="bg-white rounded-2xl overflow-hidden border border-orange-100 hover:border-red-400 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
                x-data="{ qty: 1 }"
            >
                <div>
                    <!-- Thumbnail Link to Sauce Detail -->
                    <a href="{{ route('sauces.show', $sauce->slug) }}" class="block relative aspect-[4/3] overflow-hidden bg-gray-900 cursor-pointer">
                        <img 
                            src="{{ $sauce->image }}" 
                            alt="{{ $sauce->name }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute top-3 left-3 bg-black/70 backdrop-blur-xs text-white text-xs font-extrabold px-3 py-1 rounded-full flex items-center gap-1.5">
                            <span>{{ $sauce->icon }}</span>
                            <span>{{ $sauce->name }}</span>
                        </div>
                    </a>

                    <!-- Sauce Content Info -->
                    <div class="p-5 space-y-3">
                        <div>
                            <a href="{{ route('sauces.show', $sauce->slug) }}" class="font-black text-lg text-gray-900 group-hover:text-red-600 transition-colors block">
                                {{ $sauce->name }}
                            </a>
                            <p class="text-xs font-bold text-amber-700 mt-0.5 line-clamp-1">
                                {{ $sauce->subtitle }}
                            </p>
                        </div>

                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">
                            {{ $sauce->description }}
                        </p>

                        <div class="pt-2 flex items-baseline justify-between border-t border-gray-100">
                            <div>
                                <span class="text-xl font-black text-red-600">
                                    {{ number_format($sauce->price, 0, ',', '.') }}đ
                                </span>
                                <span class="text-[11px] font-semibold text-gray-400">/ phần</span>
                            </div>
                            <a href="{{ route('menu', ['sauce' => $sauce->slug]) }}" class="text-[11px] font-bold text-red-600 hover:underline">
                                Xem món dùng sốt này →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card Footer: Quantity Selector & Add Sauce to Cart -->
                <div class="p-5 pt-0">
                    <div class="flex items-center gap-2">
                        <!-- Quantity Counter -->
                        <div class="flex items-center border border-gray-200 rounded-full bg-gray-50 px-2 py-1">
                            <button 
                                @click="if(qty > 1) qty--" 
                                type="button" 
                                class="w-6 h-6 rounded-full hover:bg-white text-xs font-black text-gray-700 flex items-center justify-center transition-colors"
                            >-</button>
                            <span class="w-7 text-center font-black text-xs text-gray-900" x-text="qty">1</span>
                            <button 
                                @click="qty++" 
                                type="button" 
                                class="w-6 h-6 rounded-full hover:bg-white text-xs font-black text-gray-700 flex items-center justify-center transition-colors"
                            >+</button>
                        </div>

                        <!-- Add to Cart Button -->
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
                            class="flex-1 py-2.5 px-4 rounded-full bg-red-600 hover:bg-red-700 text-white font-black text-xs shadow-sm hover:shadow-md transition-all active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                            <span>+ Thêm vào giỏ</span>
                        </button>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
