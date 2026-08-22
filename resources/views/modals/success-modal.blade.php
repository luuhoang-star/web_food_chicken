<!-- MODAL 3: ĐẶT ĐƠN THÀNH CÔNG -->
<div 
    x-show="openSuccessModal" 
    class="fixed inset-0 z-50 overflow-y-auto" 
    role="dialog" 
    aria-modal="true"
    x-cloak
>
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="openSuccessModal = false"></div>

        <div class="inline-block bg-white rounded-3xl text-center overflow-hidden shadow-2xl p-8 max-w-sm w-full relative z-10 space-y-5">
            <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-4xl shadow-inner animate-bounce">
                🎉
            </div>

            <div class="space-y-1.5">
                <h3 class="text-2xl font-black text-gray-900">ĐẶT HÀNG THÀNH CÔNG!</h3>
                <p class="text-xs text-gray-500">Mã đơn hàng của bạn là: <strong class="text-red-600 text-sm" x-text="orderSuccessData.orderCode">#GAO-83921</strong></p>
            </div>

            <div class="bg-[#FAF6F0] p-4 rounded-2xl text-xs space-y-1.5 text-left border border-orange-100">
                <div class="flex justify-between">
                    <span class="text-gray-500">Người nhận:</span>
                    <span class="font-bold text-gray-900" x-text="checkoutForm.fullName || 'Khách hàng'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Số điện thoại:</span>
                    <span class="font-bold text-gray-900" x-text="checkoutForm.phone"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Địa chỉ:</span>
                    <span class="font-bold text-gray-900 truncate max-w-[180px]" x-text="checkoutForm.address + ', ' + checkoutForm.district"></span>
                </div>
                <div class="flex justify-between pt-1 border-t border-gray-200">
                    <span class="text-gray-500">Tổng tiền:</span>
                    <span class="font-black text-red-600" x-text="formatCurrency(orderSuccessData.totalAmount)"></span>
                </div>
            </div>

            <p class="text-[11px] text-gray-500 italic">Bếp đang chuẩn bị món nóng giòn. Shipper sẽ giao đến bạn sau 25-40 phút!</p>

            <button 
                @click="openSuccessModal = false; switchView('home')" 
                class="w-full py-3 rounded-full bg-red-600 text-white font-bold text-xs uppercase tracking-wider shadow-md hover:bg-red-700 transition-all"
            >
                Tiếp tục đặt món
            </button>
        </div>
    </div>
</div>
