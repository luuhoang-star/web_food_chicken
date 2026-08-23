<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị')</title>
    
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
                    colors: {
                        gao: {
                            50: '#fff5f5',
                            100: '#ffe3e3',
                            200: '#ffc9c9',
                            300: '#ffa8a8',
                            400: '#fa5252',
                            500: '#e03120',
                            600: '#d12413',
                            700: '#b81c0c',
                            800: '#96170a',
                            900: '#7a1409',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(224, 49, 32, 0.12)',
                        'card': '0 4px 25px 0 rgba(0, 0, 0, 0.05)',
                        'floating': '0 20px 40px -10px rgba(209, 36, 19, 0.3)',
                    }
                }
            }
        }
    </script>

    <!-- Dynamic Database Data Injection for Frontend -->
    <script>
        window.GAO_DATA = {
            categories: @json($categories ?? []),
            sauces: @json($sauces ?? []),
            spiceLevels: @json($spiceLevels ?? []),
            toppings: @json($toppings ?? []),
            products: @json($allProducts ?? $products ?? []),
            popularDishes: @json($popularDishes ?? []),
            combos: @json($combos ?? []),
            upsellItems: @json($upsellItems ?? [])
        };
    </script>

    <!-- GAO Store Alpine State -->
    <script src="{{ asset('js/gao-store.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #faf6f0;
            color: #1a1a1a;
        }
        [x-cloak] { display: none !important; }
        
        .hero-glow {
            background: radial-gradient(circle at 60% 50%, rgba(254, 215, 170, 0.35) 0%, rgba(250, 246, 240, 0) 70%);
        }
        
        .red-glow {
            box-shadow: 0 10px 30px -5px rgba(209, 36, 19, 0.4);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #faf6f0;
        }
        ::-webkit-scrollbar-thumb {
            background: #e2d8cb;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d12413;
        }
    </style>
</head>
<body x-data="gaoApp()" class="antialiased text-gray-800 bg-[#FAF6F0] selection:bg-red-500 selection:text-white">

    <!-- Header Module -->
    @include('partials.header')

    <!-- Main Content Slot -->
    @yield('content')

    <!-- Footer Module -->
    @include('partials.footer')

    <!-- Mobile Sticky Action Bar -->
    @include('partials.mobile-sticky-bar')

    <!-- Floating Contact Widget (Messenger & Zalo) -->
    @include('partials.floating-contact')

    <!-- Floating Cart Button (Desktop) -->
    @include('partials.floating-cart')

    <!-- Modal: Tuỳ Chỉnh Món Ăn -->
    @include('modals.customize-dish')

    <!-- Drawer: Giỏ Hàng Của Bạn -->
    @include('modals.cart-drawer')

    <!-- Modal: Thanh Toán Nhanh (Hà Nội) -->
    @include('modals.checkout-modal')

    <!-- Modal: Đặt Đơn Thành Công -->
    @include('modals.success-modal')

    <!-- Toast Notification -->
    @include('partials.toast')

</body>
</html>
