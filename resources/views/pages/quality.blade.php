@extends('layouts.app')

@section('title', 'Cam Kết Chất Lượng | GAO - Gà Sốt & Cơm Hà Nội')

@section('content')
<div class="py-10 lg:py-16 bg-[#FAF6F0]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- Header / Banner Tiêu Đề -->
        <div class="text-center space-y-4 max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-red-50 text-red-600 text-xs font-black uppercase tracking-wider shadow-xs border border-red-200/60">
                <span>🛡️</span>
                <span>TIÊU CHUẨN VÀNG TẠI GAO</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 tracking-tight uppercase">
                CAM KẾT CHẤT LƯỢNG
            </h1>
            <p class="text-gray-600 text-sm sm:text-base leading-relaxed font-medium">
                Tại <strong>GAO</strong>, mỗi phần gà giòn và cơm dẻo trao đến tay khách hàng là một lời hứa trọn vẹn về độ tươi ngon nguyên bản, an toàn vệ sinh thực phẩm và trải nghiệm ẩm thực chuẩn vị Hà Nội.
            </p>
        </div>

        <!-- 1. CÁC CAM KẾT VÀNG (LẤY ĐỘNG TỪ BẢNG BENEFITS) -->
        <div class="space-y-6">
            <x-section-heading 
                badge="LỜI HỨA THƯƠNG HIỆU"
                badgeIcon="✨"
                title="3 TIÊU CHUẨN PHỤC VỤ HÀNG ĐẦU"
                subtitle="Đảm bảo sự an tâm và hài lòng cao nhất trong từng đơn hàng giao tận nơi."
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($benefits ?? [] as $benefit)
                    <div class="bg-white rounded-3xl p-8 border border-gray-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 group">
                        <div class="space-y-4">
                            <div class="w-16 h-16 rounded-2xl {{ $benefit->color_class ?? 'bg-red-50 text-red-600' }} flex items-center justify-center text-3xl shadow-xs group-hover:scale-110 transition-transform">
                                {{ $benefit->icon }}
                            </div>
                            <h3 class="text-xl font-black text-gray-900 group-hover:text-red-600 transition-colors">
                                {{ $benefit->title }}
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                                {{ $benefit->description }}
                            </p>
                        </div>
                        <div class="pt-4 border-t border-gray-100 flex items-center gap-2 text-xs font-extrabold text-emerald-600">
                            <span>✔</span>
                            <span>Đảm bảo 100% đúng cam kết</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-gray-500">
                        Đang cập nhật danh mục cam kết chất lượng...
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. QUY TRÌNH CHẾ BIẾN & AN TOÀN VỆ SINH -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-gray-200/80 shadow-sm grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                    <span>🍗</span> 100% GÀ TƯƠI NGUYÊN BẢN
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                    Công thức chuẩn vị • Chế biến nóng hổi ngay khi nhận đơn
                </h2>
                <div class="space-y-4 text-xs sm:text-sm text-gray-600 leading-relaxed font-medium">
                    <p class="flex items-start gap-3">
                        <span class="text-emerald-600 font-bold">✔</span>
                        <span><strong>Nguồn gốc xuất xứ rõ ràng:</strong> 100% thịt gà tươi từ các trang trại kiểm định chuẩn ATTP, tuyệt đối không dùng gà đông lạnh tồn dư.</span>
                    </p>
                    <p class="flex items-start gap-3">
                        <span class="text-emerald-600 font-bold">✔</span>
                        <span><strong>Chiên tươi trực tiếp theo từng đơn:</strong> Gà chỉ được lăn bột và chiên giòn khi shipper bắt đầu nhận đơn, giữ trọn lớp vỏ rụm và thịt mềm mọng nước.</span>
                    </p>
                    <p class="flex items-start gap-3">
                        <span class="text-emerald-600 font-bold">✔</span>
                        <span><strong>Sốt thủ công nguyên chất:</strong> Tự nấu mỗi ngày theo công thức độc quyền từ Gochujang Hàn Quốc, mật ong hoa rừng và bơ tỏi tươi.</span>
                    </p>
                </div>
                <div class="pt-2">
                    <a 
                        href="{{ route('menu') }}" 
                        class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm tracking-wide shadow-md red-glow transition-all active:scale-95"
                    >
                        <span>🍗 ĐẶT MÓN THƯỞNG THỨC NGAY</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Ảnh Minh Họa Chất Lượng -->
            <div class="relative aspect-[4/3] rounded-3xl overflow-hidden shadow-xl border-4 border-orange-50 bg-gray-900">
                <img 
                    src="https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=1000&q=80" 
                    alt="Cam kết chất lượng gà rán GAO" 
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-700"
                />
                <div class="absolute bottom-4 left-4 right-4 bg-black/75 backdrop-blur-xs p-4 rounded-2xl text-white text-xs space-y-1">
                    <div class="font-extrabold text-sm text-amber-400">🔥 Cam Kết Nóng Giòn Tận Cửa</div>
                    <div class="text-gray-300">Đóng gói giữ nhiệt chuyên dụng, đảm bảo món ăn thơm phức và giòn tan khi đến tay bạn.</div>
                </div>
            </div>
        </div>

        <!-- 3. TINH HOA 4 VỊ SỐT ĐẶC TRƯNG (DYNAMIC TỪ DATABASE) -->
        <div class="space-y-8">
            <x-section-heading 
                badge="CÔNG THỨC ĐỘC QUYỀN"
                badgeIcon="🌶️"
                title="4 VỊ SỐT THỦ CÔNG ĐẬM VỊ"
                subtitle="Được sáng tạo và hoàn thiện công phu để hòa quyện hoàn hảo cùng miếng gà giòn rụm."
                align="center"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($sauces ?? [] as $sauce)
                    <x-sauce-card :sauce="$sauce" />
                @endforeach
            </div>
        </div>

        <!-- 4. ĐÁNH GIÁ THỰC TẾ TỪ KHÁCH HÀNG (DYNAMIC TỪ DATABASE) -->
        <div class="space-y-8">
            <x-section-heading 
                badge="MINH CHỨNG CHẤT LƯỢNG"
                badgeIcon="⭐"
                title="KHÁCH ĂN ĐÁNH GIÁ"
                subtitle="Hơn 10.000+ khách hàng tại các quận Hà Nội đã tin chọn GAO mỗi ngày."
                align="center"
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimonials ?? [] as $review)
                    <x-review-card :review="$review" />
                @endforeach
            </div>
        </div>

        <!-- 5. CTA DƯỚI CÙNG -->
        <div class="bg-gradient-to-br from-[#141416] via-[#1c1d21] to-[#141416] rounded-3xl p-8 sm:p-12 text-white text-center space-y-6 border border-gray-800 shadow-xl">
            <h2 class="text-2xl sm:text-4xl font-black tracking-tight uppercase text-white">
                SẴN SÀNG THƯỞNG THỨC BỮA ĂN NÓNG HỔI?
            </h2>
            <p class="text-xs sm:text-sm text-gray-400 max-w-xl mx-auto font-medium leading-relaxed">
                Đặt món ngay để cảm nhận trọn vẹn từng miếng gà giòn rụm đẫm sốt đậm đà, giao nhanh tận cửa trong 25–40 phút!
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                <a 
                    href="{{ route('menu') }}" 
                    class="px-8 py-3.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm shadow-md red-glow transition-all active:scale-95"
                >
                    🍗 XEM THỰC ĐƠN ĐẶT MÓN
                </a>
                <a 
                    href="tel:{{ $settings['hotline'] ?? '0988868GAO' }}" 
                    class="px-8 py-3.5 rounded-full bg-gray-800 hover:bg-gray-700 text-white font-extrabold text-sm border border-gray-700 transition-colors"
                >
                    📞 HOTLINE: {{ $settings['hotline'] ?? '0988.868.GAO' }}
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
