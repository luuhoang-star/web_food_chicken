<!-- TOAST NOTIFICATION -->
<div 
    x-show="toast.show" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4 scale-90"
    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 transform translate-y-4 scale-90"
    class="fixed bottom-20 md:bottom-6 right-6 z-50 bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-gray-700"
    x-cloak
>
    <span class="text-xl">✅</span>
    <div>
        <div class="font-extrabold text-xs" x-text="toast.title">Đã thêm món!</div>
        <div class="text-[11px] text-gray-300" x-text="toast.message">Món đã được thêm vào giỏ hàng.</div>
    </div>
</div>
