<!DOCTYPE html>
<html lang="vi" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Trị Hệ Thống') | GAO Gà Sốt & Cơm</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .animate-order-alert { animation: pulse-ring 2s infinite; }
        
        @media print {
            body * { visibility: hidden !important; }
            #printable-receipt, #printable-receipt *, #receipt-print-area, #receipt-print-area * { visibility: visible !important; }
            #printable-receipt, #receipt-print-area {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 78mm !important;
                margin: 0 !important;
                padding: 3mm !important;
                background: white !important;
                color: black !important;
                border: none !important;
                font-family: monospace !important;
                font-size: 11px !important;
                line-height: 1.3 !important;
            }
        }
    </style>
</head>
<body 
    class="h-full bg-gray-50 text-gray-800 antialiased flex flex-col md:flex-row pb-16 md:pb-0" 
    x-data="{ mobileNavOpen: false }"
>

    <!-- 1. SIDEBAR (DESKTOP & TABLET - md:flex) -->
    <aside class="hidden md:flex md:w-64 bg-[#0F172A] text-slate-200 flex-col shrink-0 border-r border-slate-800 z-30 select-none">
        
        <!-- Brand Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800/80 bg-slate-950/30">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-red-500/20 group-hover:scale-105 transition-transform">
                    🍗
                </div>
                <div>
                    <span class="font-black text-base text-white tracking-tight block leading-none">GAO ADMIN</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Quản Trị Nhà Hàng</span>
                </div>
            </a>
            
            <a href="{{ route('home') }}" target="_blank" class="text-xs text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition-colors" title="Xem trang bán hàng">
                🌐
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm font-semibold overflow-y-auto scrollbar-none">
            
            <!-- NHÓM 1: VẬN HÀNH BÁN HÀNG -->
            <div class="px-3 pb-1.5 pt-1 text-[10px] font-black uppercase tracking-widest text-slate-400">
                Vận Hành Bán Hàng
            </div>

            <a 
                href="{{ route('admin.dashboard') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <span class="text-base">📊</span>
                <span>Báo Cáo Tổng Quan</span>
            </a>

            <a 
                href="{{ route('admin.orders.index') }}" 
                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <div class="flex items-center gap-3">
                    <span class="text-base">🛒</span>
                    <span>Quản Lý Đơn Hàng</span>
                </div>
                <span id="sidebar-pending-badge" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-red-500 text-white shadow-xs hidden">0</span>
            </a>

            <a 
                href="{{ route('admin.products.index') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <span class="text-base">🍗</span>
                <span>Thực Đơn Món</span>
            </a>

            <a 
                href="{{ route('admin.categories.index') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <span class="text-base">📂</span>
                <span>Danh Mục Món</span>
            </a>


            <a 
                href="{{ route('admin.sauces.index') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.sauces.*') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <span class="text-base">🥫</span>
                <span>Vị Sốt & Topping</span>
            </a>

            <a 
                href="{{ route('admin.coupons.index') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.coupons.*') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <span class="text-base">🎟️</span>
                <span>Mã Giảm Giá Voucher</span>
            </a>

            <!-- NHÓM 2: WEBSITE & CÀI ĐẶT -->
            <div class="px-3 pb-1.5 pt-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-t border-slate-800/80 mt-3">
                Giao Diện & Cài Đặt
            </div>

            <a 
                href="{{ route('admin.content.index') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.content.*') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <span class="text-base">🎨</span>
                <span>Giao Diện & Trang Chủ</span>
            </a>

            <a 
                href="{{ route('admin.settings.index') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <span class="text-base">⚙️</span>
                <span>Cài Đặt Vận Hành Quán</span>
            </a>

            <a 
                href="{{ route('admin.profile.show') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.profile.*') ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold shadow-md shadow-red-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}"
            >
                <span class="text-base">🔐</span>
                <span>Tài Khoản & Mật Khẩu</span>
            </a>
        </nav>

        <!-- User Profile & Logout -->
        <div class="p-3 border-t border-slate-800/80 bg-slate-950/60">
            <div class="flex items-center justify-between px-2 py-1.5">
                <a href="{{ route('admin.profile.show') }}" class="flex items-center gap-2.5 overflow-hidden hover:opacity-90 transition-opacity" title="Bấm để đổi mật khẩu và sửa thông tin">
                    <div class="w-8 h-8 rounded-xl bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                        👤
                    </div>
                    <div class="truncate">
                        <span class="text-xs font-bold text-white block truncate">{{ Auth::user()->name ?? 'Quản Trị Viên' }}</span>
                        <span class="text-[10px] text-slate-500 block truncate font-mono">{{ Auth::user()->email ?? 'admin@gao.vn' }}</span>
                    </div>
                </a>

                <form action="{{ route('admin.logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors cursor-pointer" title="Đăng xuất">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- 2. MOBILE SLIDE-OVER DRAWER (GIAO DIỆN ĐIỆN THOẠI) -->
    <div 
        x-show="mobileNavOpen" 
        class="fixed inset-0 z-50 md:hidden" 
        x-cloak
    >
        <!-- Backdrop -->
        <div 
            x-show="mobileNavOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/70 backdrop-blur-xs" 
            @click="mobileNavOpen = false"
        ></div>

        <!-- Drawer Content -->
        <div 
            x-show="mobileNavOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 max-w-xs w-full bg-gray-900 text-white shadow-2xl flex flex-col z-50"
        >
            <div class="h-16 px-5 flex items-center justify-between border-b border-gray-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-600 flex items-center justify-center text-white font-black text-sm">
                        🍗
                    </div>
                    <span class="font-black text-base text-white">GAO ADMIN</span>
                </div>
                <button @click="mobileNavOpen = false" class="p-2 text-gray-400 hover:text-white text-lg">✕</button>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 text-sm font-semibold overflow-y-auto">
                <div class="px-3 pb-1.5 pt-1 text-[10px] font-black uppercase tracking-wider text-gray-500">
                    Vận Hành Bán Hàng
                </div>

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <span>📊</span>
                    <span>Báo Cáo Tổng Quan</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.orders.*') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <span>🛒</span>
                        <span>Quản Lý Đơn Hàng</span>
                    </div>
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.products.*') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <span>🍗</span>
                    <span>Thực Đơn Món</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.categories.*') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <span>📂</span>
                    <span>Danh Mục Món</span>
                </a>
                <a href="{{ route('admin.sauces.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.sauces.*') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <span>🥫</span>
                    <span>Vị Sốt & Topping</span>
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.coupons.*') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <span>🎟️</span>
                    <span>Mã Giảm Giá Voucher</span>
                </a>

                <div class="px-3 pb-1.5 pt-4 text-[10px] font-black uppercase tracking-wider text-gray-500 border-t border-gray-800/80 mt-3">
                    Giao Diện & Cài Đặt
                </div>

                <a href="{{ route('admin.content.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.content.*') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <span>🎨</span>
                    <span>Giao Diện & Trang Chủ</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.settings.*') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <span>⚙️</span>
                    <span>Cài Đặt Vận Hành Quán</span>
                </a>
                <a href="{{ route('admin.profile.show') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.profile.*') ? 'bg-red-600 text-white font-bold' : 'text-gray-400 hover:bg-gray-800' }}">
                    <span>🔐</span>
                    <span>Tài Khoản & Mật Khẩu</span>
                </a>
            </nav>

            <div class="p-3 border-t border-gray-800 bg-gray-950/50 flex items-center justify-between">
                <a href="{{ route('admin.profile.show') }}" class="flex items-center gap-2.5 truncate">
                    <span class="w-8 h-8 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs shrink-0">👤</span>
                    <span class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Quản Trị Viên' }}</span>
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-2.5 py-1 rounded bg-gray-800 hover:bg-gray-700 text-xs text-red-400 font-bold">Thoát</button>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-gray-50">
        
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between shrink-0 shadow-2xs z-20 select-none">
            <div class="flex items-center gap-3">
                <!-- Nút Hamburger Mobile -->
                <button 
                    type="button" 
                    @click="mobileNavOpen = true" 
                    class="md:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-black transition-colors cursor-pointer"
                    title="Mở menu quản trị"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h2 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate flex items-center gap-2">
                    <span>📊</span>
                    <span>@yield('page_title', 'Bảng Điều Khiển')</span>
                </h2>
            </div>
            
            <div class="flex items-center gap-2.5">
                <!-- Nút Tạm Dừng / Mở Bếp Nhận Đơn (1-Chạm AJAX Rõ Ràng & Chống Nhầm Lẫn) -->
                @php
                    $storeStatus = \App\Models\SiteSetting::get('store_open_status', 'open');
                @endphp
                <button 
                    type="button" 
                    id="topbar-store-toggle-btn"
                    onclick="toggleStoreOpenStatus(this)"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-black transition-all shadow-xs cursor-pointer {{ $storeStatus === 'open' ? 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300/80' : 'bg-rose-600 text-white hover:bg-rose-700 shadow-sm animate-pulse' }}"
                    title="{{ $storeStatus === 'open' ? 'Quán đang MỞ nhận đơn. Click để Tạm dừng nhận đơn.' : 'Quán đang TẠM DỪNG nhận đơn. Click để Mở nhận đơn lại.' }}"
                    data-status="{{ $storeStatus }}"
                >
                    <span class="w-2 h-2 rounded-full {{ $storeStatus === 'open' ? 'bg-emerald-500 ring-4 ring-emerald-400/20 animate-pulse' : 'bg-white' }}" id="topbar-store-dot"></span>
                    <span id="topbar-store-status-text">
                        {{ $storeStatus === 'open' ? 'Quán: ĐANG MỞ' : 'Quán: TẠM DỪNG' }}
                    </span>
                    <span id="topbar-store-action-hint" class="hidden md:inline text-[10px] opacity-75 font-normal">
                        {{ $storeStatus === 'open' ? '(Click tạm dừng)' : '(Click mở lại)' }}
                    </span>
                </button>

                <!-- Nút Bật/Tắt & Test Chuông Báo Đơn -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/60" id="sound-control-wrapper">
                    <button 
                        type="button" 
                        id="sound-toggle-btn" 
                        onclick="toggleAudioAlert()" 
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold transition-all bg-emerald-600 hover:bg-emerald-500 text-white shadow-2xs cursor-pointer"
                        title="Bấm để Bật / Tắt chuông khi có đơn mới"
                    >
                        <span id="sound-icon">🔔</span>
                        <span id="sound-status-text" class="hidden sm:inline">Chuông: BẬT</span>
                    </button>
                    <button 
                        type="button" 
                        onclick="playOrderChime(true)" 
                        class="p-1 px-1.5 rounded-lg hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors cursor-pointer"
                        title="Thử tiếng chuông"
                    >
                        ▶️
                    </button>
                </div>

                <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors border border-slate-200/60 shadow-2xs">
                    <span>🍗 Web</span>
                    <span>↗</span>
                </a>
            </div>
        </header>

        <!-- Flash Alerts -->
        @if(session('success') || session('error') || session('info'))
            <div class="px-4 sm:px-6 pt-4 space-y-3">
                @if(session('success'))
                    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-semibold flex items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-2">
                            <span>✅</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm font-semibold flex items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-2">
                            <span>❌</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs sm:text-sm font-semibold flex items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-2">
                            <span>ℹ️</span>
                            <span>{{ session('info') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Page Dynamic Content -->
        <div class="p-4 sm:p-6 flex-1">
            @yield('content')
        </div>

    </main>

    <!-- 4. MOBILE STICKY BOTTOM BAR (THANH ĐIỀU HƯỚNG DƯỚI ĐIỆN THOẠI) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-gray-900/95 backdrop-blur-md border-t border-gray-800 z-40 px-2 py-1.5 flex items-center justify-around text-[10px] font-bold text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center py-1 px-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'text-red-500 font-black' : 'hover:text-white' }}">
            <span class="text-base leading-none mb-0.5">📊</span>
            <span>Báo Cáo</span>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-lg relative {{ request()->routeIs('admin.orders.*') ? 'text-red-500 font-black' : 'hover:text-white' }}">
            <span class="text-base leading-none mb-0.5">📋</span>
            <span>Đơn Hàng</span>
            <span id="mobile-bottom-pending-badge" class="absolute top-0 right-1 px-1.5 py-0.2 rounded-full text-[9px] font-black bg-red-600 text-white hidden">0</span>
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-lg {{ request()->routeIs('admin.products.*') ? 'text-red-500 font-black' : 'hover:text-white' }}">
            <span class="text-base leading-none mb-0.5">🍗</span>
            <span>Món Ăn</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'text-red-500 font-black' : 'hover:text-white' }}">
            <span class="text-base leading-none mb-0.5">⚙️</span>
            <span>Cài Đặt</span>
        </a>
        <button type="button" @click="mobileNavOpen = true" class="flex flex-col items-center py-1 px-2.5 rounded-lg hover:text-white">
            <span class="text-base leading-none mb-0.5">☰</span>
            <span>Thêm</span>
        </button>
    </nav>

    <!-- 5. TOAST THÔNG BÁO ĐƠN HÀNG MỚI NỔI LÊN MÀN HÌNH -->
    <div 
        id="new-order-toast" 
        class="fixed top-6 right-6 z-50 max-w-sm w-full bg-gray-900 text-white rounded-3xl p-5 shadow-2xl border-2 border-red-500 transition-all duration-300 transform translate-y-24 opacity-0 pointer-events-none"
    >
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-600 text-white flex items-center justify-center font-black text-lg animate-bounce">
                    🍗
                </div>
                <div>
                    <span class="px-2 py-0.5 rounded-md bg-red-500/20 text-red-400 text-[10px] font-black uppercase tracking-wider">ĐƠN HÀNG MỚI!</span>
                    <h4 class="font-black text-sm text-white" id="toast-order-code">#GAO-XXXX</h4>
                </div>
            </div>
            <button onclick="dismissToast()" class="text-gray-400 hover:text-white text-sm font-bold">✕</button>
        </div>

        <div class="mt-3 space-y-1 border-t border-gray-800 pt-2.5 text-xs text-gray-300">
            <div class="flex justify-between">
                <span class="text-gray-400">Khách:</span>
                <span class="font-bold text-white" id="toast-customer-name">...</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">SĐT:</span>
                <span class="font-mono text-red-400 font-bold" id="toast-customer-phone">...</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Món:</span>
                <span class="font-medium text-amber-300 truncate max-w-[180px]" id="toast-order-items">...</span>
            </div>
            <div class="flex justify-between border-t border-gray-800/80 pt-1">
                <span class="text-gray-400">Tổng thu:</span>
                <span class="font-black text-red-400 text-sm" id="toast-order-total">0đ</span>
            </div>
        </div>

        <div class="mt-3 flex items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" class="flex-1 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white font-black text-xs text-center transition-colors">
                Xử Lý Đơn Ngay →
            </a>
            <button onclick="dismissToast()" class="px-3 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-xs font-bold text-gray-300">
                Đóng
            </button>
        </div>
    </div>

    <!-- 6. SCRIPT REALTIME POLLING & WEB AUDIO CHIME -->
    <script>
        let isAudioAlertEnabled = localStorage.getItem('gao_audio_alert') !== 'false';
        let audioContext = null;
        let lastCheckedOrderId = 0;

        function updateSoundButtonUI() {
            const btn = document.getElementById('sound-toggle-btn');
            const icon = document.getElementById('sound-icon');
            const text = document.getElementById('sound-status-text');

            if (!btn) return;

            if (isAudioAlertEnabled) {
                btn.className = "inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold transition-all bg-emerald-600 text-white shadow-xs cursor-pointer";
                if (icon) icon.textContent = "🔔";
                if (text) text.textContent = "Chuông: BẬT";
            } else {
                btn.className = "inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold transition-all bg-gray-400 text-white shadow-xs cursor-pointer";
                if (icon) icon.textContent = "🔕";
                if (text) text.textContent = "Chuông: TẮT";
            }
        }

        function toggleAudioAlert() {
            isAudioAlertEnabled = !isAudioAlertEnabled;
            localStorage.setItem('gao_audio_alert', isAudioAlertEnabled ? 'true' : 'false');
            updateSoundButtonUI();

            if (isAudioAlertEnabled) {
                playOrderChime(true);
            }
        }

        function playOrderChime(isTest = false) {
            if (!isAudioAlertEnabled && !isTest) return;

            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;

                if (!audioContext) {
                    audioContext = new AudioCtx();
                }

                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }

                const now = audioContext.currentTime;
                // Chuông 3 nốt vui tươi (Ting - Tong - Tang)
                playTone(audioContext, 587.33, now, 0.15);         // D5
                playTone(audioContext, 739.99, now + 0.16, 0.18);  // F#5
                playTone(audioContext, 880.00, now + 0.35, 0.35);  // A5
            } catch (e) {
                console.warn("Audio Context alert:", e);
            }
        }

        function playTone(ctx, freq, startTime, duration) {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, startTime);

            gain.gain.setValueAtTime(0.001, startTime);
            gain.gain.exponentialRampToValueAtTime(0.35, startTime + 0.04);
            gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(startTime);
            osc.stop(startTime + duration);
        }

        function showOrderToast(order) {
            const toast = document.getElementById('new-order-toast');
            if (!toast || !order) return;

            document.getElementById('toast-order-code').textContent = '#' + order.order_code;
            document.getElementById('toast-customer-name').textContent = order.customer_name;
            document.getElementById('toast-customer-phone').textContent = order.customer_phone;
            document.getElementById('toast-order-items').textContent = order.items_summary || 'Món gà rán & cơm';
            document.getElementById('toast-order-total').textContent = order.formatted_total;

            toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');

            setTimeout(() => {
                dismissToast();
            }, 12000);
        }

        function dismissToast() {
            const toast = document.getElementById('new-order-toast');
            if (!toast) return;
            toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
            toast.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }

        async function pollNewOrders() {
            try {
                const url = `{{ route('admin.orders.check-new') }}?last_order_id=${lastCheckedOrderId}`;
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) return;

                const data = await response.json();
                if (data.success) {
                    if (lastCheckedOrderId === 0) {
                        lastCheckedOrderId = data.current_max_id;
                    } else if (data.has_new && data.latest_order) {
                        lastCheckedOrderId = data.current_max_id;
                        playOrderChime();
                        showOrderToast(data.latest_order);
                        window.dispatchEvent(new CustomEvent('new-order-received', { detail: data }));
                    }

                    // Cập nhật badge trên Desktop Sidebar & Mobile Bottom Bar
                    const badge = document.getElementById('sidebar-pending-badge');
                    const mobileBadge = document.getElementById('mobile-bottom-pending-badge');

                    if (data.pending_total > 0) {
                        if (badge) {
                            badge.textContent = data.pending_total;
                            badge.classList.remove('hidden');
                        }
                        if (mobileBadge) {
                            mobileBadge.textContent = data.pending_total;
                            mobileBadge.classList.remove('hidden');
                        }
                    } else {
                        if (badge) badge.classList.add('hidden');
                        if (mobileBadge) mobileBadge.classList.add('hidden');
                    }
                }
            } catch (err) {
                console.debug("Poll orders error:", err);
            }
        }

        async function toggleStoreOpenStatus(btn) {
            try {
                btn.style.opacity = '0.5';
                const res = await fetch('{{ route('admin.settings.toggle-store-status') }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                btn.style.opacity = '1';
                const data = await res.json();
                if (res.ok && data.success) {
                    const isOpen = data.store_open_status === 'open';
                    btn.setAttribute('data-status', data.store_open_status);
                    const textSpan = document.getElementById('topbar-store-status-text');
                    const dotSpan = document.getElementById('topbar-store-dot');
                    const hintSpan = document.getElementById('topbar-store-action-hint');

                    if (isOpen) {
                        btn.className = 'inline-flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-black transition-all shadow-xs cursor-pointer bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300';
                        btn.title = 'Quán đang MỞ nhận đơn. Click để Tạm dừng nhận đơn.';
                        if (textSpan) textSpan.textContent = 'Quán: ĐANG MỞ';
                        if (dotSpan) dotSpan.className = 'w-2 h-2 rounded-full bg-emerald-500';
                        if (hintSpan) hintSpan.textContent = '(Click tạm dừng)';
                    } else {
                        btn.className = 'inline-flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-black transition-all shadow-xs cursor-pointer bg-rose-600 text-white hover:bg-rose-700 shadow-sm animate-pulse';
                        btn.title = 'Quán đang TẠM DỪNG nhận đơn. Click để Mở nhận đơn lại.';
                        if (textSpan) textSpan.textContent = 'Quán: TẠM DỪNG';
                        if (dotSpan) dotSpan.className = 'w-2 h-2 rounded-full bg-white';
                        if (hintSpan) hintSpan.textContent = '(Click mở lại)';
                    }
                }
            } catch (e) {
                btn.style.opacity = '1';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateSoundButtonUI();
            pollNewOrders();
            setInterval(pollNewOrders, 12000);
        });
    </script>

</body>
</html>
