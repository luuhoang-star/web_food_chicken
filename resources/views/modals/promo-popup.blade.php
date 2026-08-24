<!-- PROMO POPUP MODAL (MARKETING EVENT) -->
@if(($settings['popup_enabled'] ?? '0') == '1')
<div 
    x-data="{ showPromoPopup: false }"
    x-init="
        if (!sessionStorage.getItem('gao_popup_seen')) {
            setTimeout(() => { showPromoPopup = true; }, 1200);
        }
    "
    x-show="showPromoPopup" 
    class="fixed inset-0 z-50 overflow-y-auto" 
    aria-labelledby="promo-popup-title" 
    role="dialog" 
    aria-modal="true"
    x-cloak
>
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Backdrop -->
        <div 
            x-show="showPromoPopup"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/70 backdrop-blur-xs transition-opacity" 
            @click="showPromoPopup = false; sessionStorage.setItem('gao_popup_seen', '1')"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Popup Box -->
        <div 
            x-show="showPromoPopup"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-6 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-6 sm:scale-95"
            class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full relative border border-orange-200"
        >
            <!-- Close Button -->
            <button 
                @click="showPromoPopup = false; sessionStorage.setItem('gao_popup_seen', '1')"
                class="absolute top-3.5 right-3.5 z-20 w-8 h-8 rounded-full bg-black/60 hover:bg-black text-white flex items-center justify-center transition-colors cursor-pointer"
                title="Đóng popup"
            >
                ✕
            </button>

            <!-- Banner Image (If set) -->
            @if(!empty($settings['popup_banner_image']))
                <div class="h-48 sm:h-56 w-full overflow-hidden relative bg-orange-100">
                    <img 
                        src="{{ str_starts_with($settings['popup_banner_image'], 'http') ? $settings['popup_banner_image'] : asset($settings['popup_banner_image']) }}" 
                        alt="{{ $settings['popup_title'] ?? 'Ưu đãi' }}" 
                        class="w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                </div>
            @else
                <div class="h-28 bg-gradient-to-r from-red-600 via-orange-600 to-amber-500 flex items-center justify-center text-4xl text-white">
                    🍗🔥
                </div>
            @endif

            <!-- Content Area -->
            <div class="p-6 text-center space-y-3">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-700">
                    Ưu Đãi Đặc Biệt
                </span>

                <h3 class="text-xl font-black text-gray-900 tracking-tight" id="promo-popup-title">
                    {{ $settings['popup_title'] ?? '🎉 Ưu Đãi Đặc Biệt Hôm Nay!' }}
                </h3>

                <p class="text-xs text-gray-600 leading-relaxed">
                    {{ $settings['popup_description'] ?? 'Tặng ngay 01 hũ sốt đặc trưng hoặc Freeship 3km cho đơn hàng từ 100k hôm nay. Đặt ngay để nhận ưu đãi!' }}
                </p>

                <div class="pt-2 flex flex-col gap-2">
                    <a 
                        href="{{ $settings['popup_cta_url'] ?? route('menu') }}" 
                        @click="sessionStorage.setItem('gao_popup_seen', '1')"
                        class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-600/30 transition-all hover:scale-[1.02] active:scale-95 text-center block"
                    >
                        {{ $settings['popup_cta_text'] ?? 'Xem Thực Đơn Đặt Ngay →' }}
                    </a>

                    <button 
                        @click="showPromoPopup = false; sessionStorage.setItem('gao_popup_seen', '1')"
                        type="button" 
                        class="text-xs text-gray-400 hover:text-gray-600 font-semibold py-1"
                    >
                        Để sau, tôi muốn xem menu trước
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
