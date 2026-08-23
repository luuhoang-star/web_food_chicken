<!-- MODAL 2: THANH TOÁN NHANH (HÀ NỘI) - MATCHING SCREENSHOT 5 & 6 EXACTLY -->
<div 
    x-show="openCheckoutModal" 
    class="fixed inset-0 z-50 overflow-y-auto" 
    aria-labelledby="modal-checkout-title" 
    role="dialog" 
    aria-modal="true"
    x-cloak
>
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Backdrop -->
        <div 
            x-show="openCheckoutModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
            @click="openCheckoutModal = false"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Content Box -->
        <div 
            x-show="openCheckoutModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full max-h-[92vh] flex flex-col"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                <h3 class="text-xl font-black text-gray-900" id="modal-checkout-title">
                    Thanh toán nhanh (Hà Nội)
                </h3>
                <button 
                    @click="openCheckoutModal = false" 
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="px-6 py-5 overflow-y-auto space-y-5 flex-1">
                
                <!-- Delivery Time Banner -->
                <div class="bg-[#FFF5F5] border border-red-100 rounded-2xl p-3.5 flex items-center gap-2.5 text-xs sm:text-sm font-bold text-red-600">
                    <span class="text-lg">🛵</span>
                    <span><strong>Giao tiêu chuẩn:</strong> Dự kiến đến sau 25 – 40 phút</span>
                </div>

                <!-- Input 1: Họ và tên -->
                <div class="space-y-1.5">
                    <label class="block text-xs sm:text-sm font-black text-gray-900">
                        Họ và tên *
                    </label>
                    <input 
                        type="text" 
                        x-model="checkoutForm.fullName"
                        placeholder="Ví dụ: Nguyễn Văn A"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                    >
                </div>

                <!-- Input 2: Số điện thoại -->
                <div class="space-y-1.5">
                    <label class="block text-xs sm:text-sm font-black text-gray-900">
                        Số điện thoại *
                    </label>
                    <input 
                        type="tel" 
                        x-model="checkoutForm.phone"
                        placeholder="Ví dụ: 0912 345 678"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                    >
                </div>

                <!-- Input 3: Khu vực quận (Hà Nội) -->
                <div class="space-y-1.5">
                    <label class="block text-xs sm:text-sm font-black text-gray-900">
                        Khu vực quận (Hà Nội) *
                    </label>
                    <div class="relative">
                        <select 
                            x-model="checkoutForm.district"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-bold text-gray-800 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 appearance-none pr-10 cursor-pointer"
                        >
                            <template x-for="dist in districts" :key="dist">
                                <option :value="dist" x-text="dist"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Input 4: Địa chỉ chi tiết -->
                <div class="space-y-1.5">
                    <label class="block text-xs sm:text-sm font-black text-gray-900">
                        Địa chỉ chi tiết *
                    </label>
                    <input 
                        type="text" 
                        x-model="checkoutForm.address"
                        placeholder="Số nhà, ngõ, tên toà nhà..."
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                    >
                </div>

                <!-- Input 5: Ghi chú cho tài xế giao hàng -->
                <div class="space-y-1.5">
                    <label class="block text-xs sm:text-sm font-black text-gray-900">
                        Ghi chú cho tài xế giao hàng
                    </label>
                    <input 
                        type="text" 
                        x-model="checkoutForm.driverNote"
                        placeholder="Ví dụ: Gọi trước khi đến, gửi bảo vệ..."
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                    >
                </div>

                <!-- Section: Phương thức thanh toán -->
                <div class="space-y-2.5 pt-1">
                    <label class="block text-xs sm:text-sm font-black text-gray-900">
                        Phương thức thanh toán
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        
                        <!-- Option 1: COD -->
                        <button 
                            @click="checkoutForm.paymentMethod = 'cod'"
                            type="button"
                            class="p-3 rounded-2xl border-2 text-left flex items-center gap-2 transition-all cursor-pointer"
                            :class="checkoutForm.paymentMethod === 'cod' ? 'border-red-500 bg-red-50/80 shadow-xs ring-2 ring-red-500/20' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                        >
                            <span class="text-base">💵</span>
                            <span class="text-xs font-black text-gray-900">Tiền mặt (COD)</span>
                        </button>

                        <!-- Option 2: VietQR Chuyển khoản -->
                        <button 
                            @click="checkoutForm.paymentMethod = 'bank_transfer'"
                            type="button"
                            class="p-3 rounded-2xl border-2 text-left flex items-center gap-2 transition-all cursor-pointer"
                            :class="checkoutForm.paymentMethod === 'bank_transfer' ? 'border-red-500 bg-red-50/80 shadow-xs ring-2 ring-red-500/20' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                        >
                            <span class="text-base">📱</span>
                            <span class="text-xs font-black text-gray-900">Quét mã VietQR</span>
                        </button>

                        <!-- Option 3: MoMo -->
                        <button 
                            @click="checkoutForm.paymentMethod = 'momo'"
                            type="button"
                            class="p-3 rounded-2xl border-2 text-left flex items-center gap-2 transition-all cursor-pointer"
                            :class="checkoutForm.paymentMethod === 'momo' ? 'border-red-500 bg-red-50/80 shadow-xs ring-2 ring-red-500/20' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                        >
                            <span class="w-3.5 h-3.5 rounded-full bg-[#A50064] inline-block shrink-0"></span>
                            <span class="text-xs font-black text-gray-900">Ví MoMo</span>
                        </button>

                    </div>

                    <!-- VietQR Detail Box (Shows when VietQR is selected) -->
                    <div 
                        x-show="checkoutForm.paymentMethod === 'bank_transfer'" 
                        class="p-4 bg-white rounded-2xl border border-red-200 space-y-3 shadow-xs"
                    >
                        <div class="flex items-center justify-between text-xs font-black text-red-600">
                            <span>⚡ Quét mã QR thanh toán nhanh</span>
                            <span>MB Bank</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="w-32 h-32 p-1.5 bg-white border border-gray-200 rounded-xl shadow-xs shrink-0">
                                <img 
                                    :src="'https://img.vietqr.io/image/MB-0988888888-compact2.png?amount=' + (totalPrice >= 100000 ? totalPrice : totalPrice + 15000) + '&addInfo=GAO%20' + encodeURIComponent(checkoutForm.phone || 'DONHANG') + '&accountName=GAO%20CHICKEN%20HA%20NOI'" 
                                    alt="VietQR Code" 
                                    class="w-full h-full object-contain"
                                >
                            </div>
                            <div class="space-y-1.5 text-xs flex-1 w-full">
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="text-gray-500">Ngân hàng:</span>
                                    <span class="font-black text-gray-900">MB Bank</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="text-gray-500">Số tài khoản:</span>
                                    <span class="font-black text-red-600 tracking-wider">0988 888 888</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="text-gray-500">Chủ tài khoản:</span>
                                    <span class="font-bold text-gray-900">GAO CHICKEN</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nội dung CK:</span>
                                    <span class="font-black text-gray-900" x-text="'GAO ' + (checkoutForm.phone || 'DONHANG')">GAO 0912345678</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Box: MÓN ĐÃ CHỌN -->
                <div class="bg-[#FAF6F0] rounded-2xl p-4 border border-orange-200/60 space-y-3">
                    <div class="text-xs font-black text-gray-700 uppercase tracking-wider">
                        MÓN ĐÃ CHỌN:
                    </div>

                    <div class="space-y-1.5 text-xs font-bold text-gray-800">
                        <template x-for="(item, idx) in cartItems" :key="idx">
                            <div class="flex items-center justify-between">
                                <div class="truncate pr-4 text-gray-700">
                                    <span x-text="item.quantity + '× ' + item.name"></span>
                                </div>
                                <div class="font-black text-gray-900 shrink-0" x-text="formatCurrency(item.price * item.quantity)">
                                    147.000đ
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Total Summary -->
                    <div class="pt-3 border-t border-dashed border-gray-300 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-900">Tổng thanh toán:</span>
                        <span class="text-xl font-black text-red-600" x-text="formatCurrency(totalPrice >= 100000 ? totalPrice : totalPrice + 15000)">
                            313.000đ
                        </span>
                    </div>
                </div>

            </div>

            <!-- Modal Footer: Submit Order Button -->
            <div class="p-4 sm:p-5 bg-white border-t border-gray-100 shrink-0">
                <button 
                    @click="submitOrder()" 
                    type="button" 
                    class="w-full py-4 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-base tracking-wide uppercase shadow-xl red-glow text-center transition-all active:scale-95"
                >
                    Xác nhận đặt đơn
                </button>
            </div>

        </div>
    </div>
</div>
