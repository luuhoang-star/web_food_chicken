<!-- MODAL 2: THANH TOÁN NHANH (HÀ NỘI) - DYNAMIC VIETQR & COUPONS & SETTINGS -->
<div 
    x-show="openCheckoutModal" 
    class="fixed inset-0 z-50 overflow-y-auto" 
    aria-labelledby="modal-checkout-title" 
    role="dialog" 
    aria-modal="true"
    x-cloak
    x-data="{
        appliedCoupon: null,
        couponInput: '',
        couponError: '',
        couponSuccess: '',
        isApplyingCoupon: false,

        copiedField: '',
        copyText(text, fieldName) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
            } else {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            }
            this.copiedField = fieldName;
            setTimeout(() => {
                if (this.copiedField === fieldName) this.copiedField = '';
            }, 2000);
        },

        getShippingFee() {
            const threshold = Number('{{ (int) ($settings['freeship_threshold'] ?? 100000) }}');
            const defaultFee = Number('{{ (int) ($settings['shipping_base_fee'] ?? ($settings['shipping_fee_default'] ?? 15000)) }}');
            return (this.totalPrice >= threshold) ? 0 : defaultFee;
        },

        getDiscountAmount() {
            return this.appliedCoupon ? Number(this.appliedCoupon.discount_amount) : 0;
        },

        getFinalTotal() {
            const ship = this.getShippingFee();
            const discount = this.getDiscountAmount();
            return Math.max(0, this.totalPrice + ship - discount);
        },

        async applyCouponCode() {
            if (!this.couponInput.trim()) {
                this.couponError = 'Vui lòng nhập mã giảm giá.';
                this.couponSuccess = '';
                return;
            }
            this.isApplyingCoupon = true;
            this.couponError = '';
            this.couponSuccess = '';

            try {
                const res = await fetch('{{ route('coupons.apply') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : ''
                    },
                    body: JSON.stringify({
                        code: this.couponInput.trim(),
                        subtotal: this.totalPrice
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.appliedCoupon = data;
                    this.couponSuccess = data.message;
                    this.couponError = '';
                    this.checkoutForm.couponCode = data.coupon_code;
                    this.checkoutForm.discount = data.discount_amount;
                } else {
                    this.appliedCoupon = null;
                    this.couponError = data.message || 'Mã giảm giá không hợp lệ.';
                    this.couponSuccess = '';
                    this.checkoutForm.couponCode = '';
                    this.checkoutForm.discount = 0;
                }
            } catch (err) {
                this.couponError = 'Lỗi kết nối máy chủ khi áp dụng mã.';
            } finally {
                this.isApplyingCoupon = false;
            }
        },

        removeCoupon() {
            this.appliedCoupon = null;
            this.couponInput = '';
            this.couponSuccess = '';
            this.couponError = '';
            this.checkoutForm.couponCode = '';
            this.checkoutForm.discount = 0;
        }
    }"
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
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="px-6 py-5 overflow-y-auto space-y-5 flex-1">
                
                <!-- Delivery Time Banner -->
                <div class="bg-[#FFF5F5] border border-red-100 rounded-2xl p-3.5 flex items-center gap-2.5 text-xs sm:text-sm font-bold text-red-600">
                    <span class="text-lg">🛵</span>
                    <span><strong>Giao tiêu chuẩn:</strong> Dự kiến đến sau {{ $settings['delivery_estimated_time'] ?? '25 – 40 phút' }}</span>
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

                <!-- Section: Mã Giảm Giá / Voucher Ưu Đãi -->
                <div class="space-y-2 pt-1 border-t border-gray-100">
                    <label class="block text-xs sm:text-sm font-black text-gray-900 flex items-center gap-1.5">
                        <span>🏷️</span>
                        <span>Mã Giảm Giá / Voucher</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <input 
                            type="text" 
                            x-model="couponInput"
                            placeholder="Nhập mã (VD: GAO20K)..."
                            :disabled="appliedCoupon !== null"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs sm:text-sm font-bold uppercase tracking-wider focus:outline-none focus:border-red-500 font-mono disabled:bg-gray-100"
                        >
                        <template x-if="!appliedCoupon">
                            <button 
                                @click="applyCouponCode()"
                                type="button" 
                                :disabled="isApplyingCoupon"
                                class="px-4 py-2.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-black transition-colors shrink-0 disabled:opacity-50 cursor-pointer"
                            >
                                <span x-show="!isApplyingCoupon">Áp Dụng</span>
                                <span x-show="isApplyingCoupon">Đang check...</span>
                            </button>
                        </template>
                        <template x-if="appliedCoupon">
                            <button 
                                @click="removeCoupon()"
                                type="button" 
                                class="px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors shrink-0 cursor-pointer"
                            >
                                ✕ Huỷ mã
                            </button>
                        </template>
                    </div>

                    <div x-show="couponSuccess" class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                        <span>✅</span>
                        <span x-text="couponSuccess"></span>
                    </div>

                    <div x-show="couponError" class="text-[11px] font-bold text-rose-600 flex items-center gap-1">
                        <span>❌</span>
                        <span x-text="couponError"></span>
                    </div>
                </div>

                <!-- Section: Phương thức thanh toán -->
                <div class="space-y-2.5 pt-1">
                    <label class="block text-xs sm:text-sm font-black text-gray-900">
                        Phương thức thanh toán
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        
                        <!-- Option 1: COD -->
                        @if(($settings['payment_cod_enabled'] ?? '1') == '1')
                        <button 
                            @click="checkoutForm.paymentMethod = 'cod'"
                            type="button"
                            class="p-3 rounded-2xl border-2 text-left flex items-center gap-2 transition-all cursor-pointer"
                            :class="checkoutForm.paymentMethod === 'cod' ? 'border-red-500 bg-red-50/80 shadow-xs ring-2 ring-red-500/20' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                        >
                            <span class="text-base">💵</span>
                            <span class="text-xs font-black text-gray-900">Tiền mặt (COD)</span>
                        </button>
                        @endif

                        <!-- Option 2: VietQR Chuyển khoản -->
                        @if(($settings['payment_bank_enabled'] ?? '1') == '1')
                        <button 
                            @click="checkoutForm.paymentMethod = 'bank_transfer'"
                            type="button"
                            class="p-3 rounded-2xl border-2 text-left flex items-center gap-2 transition-all cursor-pointer"
                            :class="checkoutForm.paymentMethod === 'bank_transfer' ? 'border-red-500 bg-red-50/80 shadow-xs ring-2 ring-red-500/20' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                        >
                            <span class="text-base">📱</span>
                            <span class="text-xs font-black text-gray-900">Quét VietQR</span>
                        </button>
                        @endif

                        <!-- Option 3: MoMo -->
                        @if(($settings['payment_momo_enabled'] ?? '1') == '1')
                        <button 
                            @click="checkoutForm.paymentMethod = 'momo'"
                            type="button"
                            class="p-3 rounded-2xl border-2 text-left flex items-center gap-2 transition-all cursor-pointer"
                            :class="checkoutForm.paymentMethod === 'momo' ? 'border-red-500 bg-red-50/80 shadow-xs ring-2 ring-red-500/20' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                        >
                            <span class="w-3.5 h-3.5 rounded-full bg-[#A50064] inline-block shrink-0"></span>
                            <span class="text-xs font-black text-gray-900">Ví MoMo</span>
                        </button>
                        @endif

                    </div>

                    <!-- VietQR Detail Box (Shows when VietQR is selected) -->
                    <div 
                        x-show="checkoutForm.paymentMethod === 'bank_transfer'" 
                        class="p-4 bg-white rounded-2xl border border-red-200 space-y-3 shadow-xs"
                    >
                        <div class="flex items-center justify-between text-xs font-black text-red-600">
                            <span class="flex items-center gap-1.5">
                                <span>⚡</span>
                                <span>Quét mã QR thanh toán nhanh</span>
                            </span>
                            <span class="text-gray-700 bg-gray-100 px-2 py-0.5 rounded-md font-bold text-[11px]">{{ $settings['bank_name'] ?? 'Vietcombank' }}</span>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <!-- Mã QR to rõ sắc nét -->
                            <div class="w-36 h-36 sm:w-40 sm:h-40 p-1.5 bg-white border border-gray-200 rounded-2xl shadow-sm shrink-0 flex items-center justify-center">
                                <img 
                                    :src="'https://img.vietqr.io/image/{{ $settings['bank_code'] ?? 'MB' }}-{{ $settings['bank_account_number'] ?? '0988888888' }}-compact2.png?amount=' + getFinalTotal() + '&addInfo={{ urlencode($settings['bank_transfer_prefix'] ?? 'HUBBY') }}%20' + encodeURIComponent(checkoutForm.phone || 'DONHANG') + '&accountName={{ urlencode($settings['bank_account_holder'] ?? 'GAO CHICKEN HA NOI') }}'" 
                                    alt="VietQR Code" 
                                    class="w-full h-full object-contain rounded-xl"
                                >
                            </div>

                            <!-- Thông tin chuyển khoản + Nút sao chép 1 chạm -->
                            <div class="space-y-2 text-xs flex-1 w-full">
                                <div class="flex items-center justify-between border-b border-gray-100 pb-1.5">
                                    <span class="text-gray-500">Số tiền:</span>
                                    <span class="font-black text-red-600 text-sm" x-text="formatCurrency(getFinalTotal())">0đ</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-gray-100 pb-1.5">
                                    <span class="text-gray-500">Số tài khoản:</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-black text-gray-900 tracking-wider">{{ $settings['bank_account_number'] ?? '0988 888 888' }}</span>
                                        <button 
                                            type="button" 
                                            @click="copyText('{{ $settings['bank_account_number'] ?? '0988888888' }}', 'stk')"
                                            class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 transition-all flex items-center gap-1 cursor-pointer"
                                            title="Sao chép số tài khoản"
                                        >
                                            <span x-text="copiedField === 'stk' ? '✓ Đã chép' : '📋 Sao chép'"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between border-b border-gray-100 pb-1.5">
                                    <span class="text-gray-500">Chủ tài khoản:</span>
                                    <span class="font-bold text-gray-900 uppercase">{{ $settings['bank_account_holder'] ?? 'GAO CHICKEN HA NOI' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Nội dung CK:</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-black text-blue-700" x-text="'{{ $settings['bank_transfer_prefix'] ?? 'HUBBY' }} ' + (checkoutForm.phone || 'DONHANG')">HUBBY 0912345678</span>
                                        <button 
                                            type="button" 
                                            @click="copyText('{{ $settings['bank_transfer_prefix'] ?? 'HUBBY' }} ' + (checkoutForm.phone || 'DONHANG'), 'memo')"
                                            class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 transition-all flex items-center gap-1 cursor-pointer"
                                            title="Sao chép nội dung chuyển khoản"
                                        >
                                            <span x-text="copiedField === 'memo' ? '✓ Đã chép' : '📋 Sao chép'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chú thích hỗ trợ khi dùng điện thoại -->
                        <div class="text-[11px] text-gray-500 bg-amber-50/80 border border-amber-200/60 p-2 rounded-xl flex items-center gap-2">
                            <span>💡</span>
                            <span>Nếu đặt bằng điện thoại, bạn có thể bấm <b>📋 Sao chép</b> STK & Nội dung rồi dán vào app ngân hàng để chuyển khoản nhanh!</span>
                        </div>
                    </div>
                </div>

                <!-- Summary Box: MÓN ĐÃ CHỌN & GIÁ TIỀN -->
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

                    <!-- Breakdown: Subtotal, Shipping, Discount -->
                    <div class="pt-2 border-t border-gray-200/80 text-xs space-y-1 font-semibold text-gray-600">
                        <div class="flex justify-between">
                            <span>Tiền món:</span>
                            <span class="font-bold text-gray-900" x-text="formatCurrency(totalPrice)">0đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí giao hàng:</span>
                            <span class="font-bold" :class="getShippingFee() === 0 ? 'text-emerald-600' : 'text-gray-900'" x-text="getShippingFee() === 0 ? 'Miễn phí (Freeship)' : formatCurrency(getShippingFee())"></span>
                        </div>
                        <template x-if="appliedCoupon">
                            <div class="flex justify-between text-emerald-600 font-bold">
                                <span>Giảm giá (<span x-text="appliedCoupon.coupon_code"></span>):</span>
                                <span x-text="'-' + formatCurrency(getDiscountAmount())"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Total Summary -->
                    <div class="pt-3 border-t border-dashed border-gray-300 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-900">Tổng thanh toán:</span>
                        <span class="text-xl font-black text-red-600" x-text="formatCurrency(getFinalTotal())">
                            0đ
                        </span>
                    </div>
                </div>

            </div>

            <!-- Modal Footer: Submit Order Button -->
            <div class="p-4 sm:p-5 bg-white border-t border-gray-100 shrink-0">
                @if(($settings['store_open_status'] ?? 'open') === 'paused')
                    <div class="space-y-2.5">
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-bold text-rose-800 text-center">
                            ⚠️ Bếp GAO đang tạm dừng nhận đơn ít phút (đang xử lý giao hàng). Quý khách vui lòng gọi Hotline: <a href="tel:{{ $settings['hotline'] ?? '0988.868.GAO' }}" class="underline font-black text-rose-900">{{ $settings['hotline'] ?? '0988.868.GAO' }}</a> nếu cần gấp nhé!
                        </div>
                        <button 
                            type="button" 
                            disabled
                            class="w-full py-4 rounded-full bg-gray-300 text-gray-500 font-black text-sm tracking-wide uppercase text-center cursor-not-allowed"
                        >
                            Bếp Đang Tạm Dừng Nhận Đơn
                        </button>
                    </div>
                @else
                    @if(!empty($settings['rush_hour_note']))
                        <div class="mb-3 p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-[11px] font-medium text-amber-800 flex items-center gap-1.5">
                            <span>⏰</span>
                            <span>{{ $settings['rush_hour_note'] }}</span>
                        </div>
                    @endif
                    <button 
                        @click="submitOrder()" 
                        type="button" 
                        class="w-full py-4 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-base tracking-wide uppercase shadow-xl red-glow text-center transition-all active:scale-95 cursor-pointer"
                    >
                        Xác nhận đặt đơn
                    </button>
                @endif
            </div>

        </div>
    </div>
</div>
