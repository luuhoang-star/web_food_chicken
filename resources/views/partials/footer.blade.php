<!-- FOOTER -->
<footer class="bg-[#141416] text-white pt-16 pb-28 md:pb-16 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10">
            
            <div class="lg:col-span-4 space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center text-white shadow-md">
                        <span class="text-lg">🍗</span>
                    </div>
                    <div>
                        <span class="text-2xl font-black tracking-tight text-white block leading-none">{{ $settings['site_name'] ?? 'GAO' }}</span>
                        <span class="text-[10px] font-bold text-gray-400 tracking-wider">{{ $settings['site_tagline'] ?? 'GÀ SỐT & CƠM' }}</span>
                    </div>
                </a>
                <p class="text-xs text-gray-400 leading-relaxed pr-4">
                    {{ $settings['footer_description'] ?? 'Thương hiệu Gà Sốt & Cơm chuẩn vị tại Hà Nội. Gà giòn rụm, đẫm sốt đậm đà, phục vụ nóng hổi tận tay khách hàng trong bán kính 3–5km.' }}
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ $settings['social_facebook'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-red-600 flex items-center justify-center text-xs font-bold text-white transition-colors">FB</a>
                    <a href="{{ $settings['social_instagram'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-red-600 flex items-center justify-center text-xs font-bold text-white transition-colors">IG</a>
                    <a href="{{ $settings['social_tiktok'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-red-600 flex items-center justify-center text-xs font-bold text-white transition-colors">TT</a>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-3">
                <h5 class="text-sm font-bold text-white uppercase tracking-wider">Thực Đơn</h5>
                <ul class="space-y-2 text-xs text-gray-400 font-medium">
                    @forelse($categories ?? [] as $cat)
                        <li>
                            <a href="{{ route('menu', ['category' => $cat->slug]) }}" class="hover:text-red-400 transition-colors">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @empty
                        <li><a href="{{ route('menu') }}" class="hover:text-red-400 transition-colors">Tất cả món</a></li>
                    @endforelse
                </ul>
            </div>

            <div class="lg:col-span-3 space-y-3">
                <h5 class="text-sm font-bold text-white uppercase tracking-wider">
                    <a href="{{ route('quality') }}" class="hover:text-red-400 transition-colors">Cam Kết & Dịch Vụ</a>
                </h5>
                <ul class="space-y-2 text-xs text-gray-400 font-medium">
                    <li><a href="{{ route('order.tracking') }}" class="hover:text-red-400 text-orange-400 font-bold transition-colors flex items-center gap-2"><span>🔍</span> <span>Tra Cứu Đơn Hàng</span></a></li>
                    <li><a href="{{ route('quality') }}" class="hover:text-gray-300 transition-colors flex items-center gap-2"><span>🛵</span> <span>Freeship 3km từ 100k</span></a></li>
                    <li><a href="{{ route('quality') }}" class="hover:text-gray-300 transition-colors flex items-center gap-2"><span>🔥</span> <span>Giao nhanh nóng hổi 25–40p</span></a></li>
                    <li><a href="{{ route('quality') }}" class="hover:text-gray-300 transition-colors flex items-center gap-2"><span>🍗</span> <span>100% Gà tươi chiên giòn</span></a></li>
                    <li><a href="{{ route('quality') }}" class="hover:text-gray-300 transition-colors flex items-center gap-2"><span>✨</span> <span>Đảm bảo vệ sinh ATTP</span></a></li>
                </ul>
            </div>

            <div class="lg:col-span-3 space-y-3">
                <h5 class="text-sm font-bold text-white uppercase tracking-wider">Thông Tin Liên Hệ</h5>
                <ul class="space-y-2.5 text-xs text-gray-400 font-medium">
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">📍</span>
                        <span>{{ $settings['store_address'] ?? 'Hà Nội: Đống Đa, Cầu Giấy, Hoàn Kiếm, Hai Bà Trưng, Ba Đình, Thanh Xuân.' }}</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-red-500">📞</span>
                        <span>Hotline: <strong class="text-white">{{ $settings['hotline'] ?? '0988.868.GAO' }}</strong></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-red-500">⏰</span>
                        <span>Giờ nhận đơn: {{ $settings['kitchen_open_time'] ?? '09:30' }} – {{ $settings['kitchen_close_time'] ?? '22:30' }} hàng ngày</span>
                    </li>
                </ul>
            </div>

        </div>

        <div class="mt-12 pt-8 border-t border-gray-800/80 text-center text-xs text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span>{{ $settings['copyright'] ?? '© 2026 GAO - Gà Sốt & Cơm Hà Nội. All rights reserved.' }}</span>
            <div class="flex items-center gap-4">
                <span>{{ $settings['footer_slogan'] ?? 'Thực đơn gà sốt đậm vị chuẩn Hà Nội' }}</span>
                <span class="text-gray-700">•</span>
                <a href="{{ route('admin.login') }}" class="text-gray-500 hover:text-gray-300 transition-colors" title="Trang Quản Trị Bếp">
                    🔒 Quản Trị
                </a>
            </div>
        </div>
    </div>
</footer>
