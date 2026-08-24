<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $settings['meta_title'] ?? 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị')</title>
    
    <!-- Meta SEO & Open Graph -->
    <meta name="description" content="{{ $settings['meta_description'] ?? 'Thương hiệu Gà Sốt & Cơm Hà Nội chuyên các món gà rán giòn rụm kết hợp cùng 4 vị sốt độc quyền chuẩn vị Hà Nội. Giao nhanh 25-40 phút.' }}">
    <meta name="keywords" content="{{ $settings['meta_keywords'] ?? 'gà sốt, gà rán hà nội, cơm gà sốt, gao gà rán' }}">
    <meta property="og:title" content="{{ $settings['meta_title'] ?? 'GAO - Gà Sốt & Cơm Hà Nội' }}">
    <meta property="og:description" content="{{ $settings['meta_description'] ?? 'Gà Giòn Sốt Đậm Vị Hà Nội - Đặt món giao nhanh 25-40 phút.' }}">
    @if(!empty($settings['og_image']))
        <meta property="og:image" content="{{ str_starts_with($settings['og_image'], 'http') ? $settings['og_image'] : asset($settings['og_image']) }}">
    @endif
    @if(!empty($settings['favicon_url']))
        <link rel="icon" href="{{ str_starts_with($settings['favicon_url'], 'http') ? $settings['favicon_url'] : asset($settings['favicon_url']) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Tracking Scripts (GA4 & FB Pixel) -->
    @if(!empty($settings['google_analytics_id']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['google_analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $settings['google_analytics_id'] }}');
        </script>
    @endif

    @if(!empty($settings['facebook_pixel_id']))
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $settings['facebook_pixel_id'] }}');
            fbq('track', 'PageView');
        </script>
    @endif

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
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
                        },
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(224, 49, 32, 0.08)',
                        'card': '0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                        'card-hover': '0 20px 35px -8px rgba(209, 36, 19, 0.12), 0 8px 16px -4px rgba(0, 0, 0, 0.04)',
                        'floating': '0 20px 40px -10px rgba(209, 36, 19, 0.35)',
                        'glow': '0 0 25px rgba(224, 49, 32, 0.3)',
                    },
                    borderRadius: {
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                        '4xl': '2rem',
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-subtle': 'bounceSubtle 2s infinite ease-in-out',
                    },
                    keyframes: {
                        bounceSubtle: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-4px)' },
                        }
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
            toppings: @json($toppings ?? []),
            products: @json($allProducts ?? $products ?? []),
            popularDishes: @json($popularDishes ?? []),
            combos: @json($combos ?? []),
            upsellItems: @json($upsellItems ?? [])
        };
        window.GAO_SETTINGS = @json($settings ?? []);
    </script>

    <!-- GAO Store Alpine State -->
    <script src="{{ asset('js/gao-store.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #faf6f0;
            color: #1a1a1a;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        [x-cloak] { display: none !important; }
        
        .hero-glow {
            background: radial-gradient(circle at 60% 40%, rgba(254, 215, 170, 0.4) 0%, rgba(254, 243, 199, 0.2) 40%, rgba(250, 246, 240, 0) 70%);
        }
        
        .red-glow {
            box-shadow: 0 10px 30px -5px rgba(209, 36, 19, 0.38);
        }

        .red-glow-lg {
            box-shadow: 0 20px 40px -10px rgba(209, 36, 19, 0.45);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #faf6f0;
        }
        ::-webkit-scrollbar-thumb {
            background: #e2d8cb;
            border-radius: 6px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d12413;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body x-data="gaoApp()" class="antialiased text-gray-800 bg-[#FAF6F0] selection:bg-red-500 selection:text-white flex flex-col min-h-screen">

    <!-- Header Module with Mobile Nav Drawer -->
    @include('partials.header')

    <!-- Main Content Slot -->
    <main class="flex-1">
        @yield('content')
    </main>

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

    <!-- Modal: Popup Khuyến Mãi Trang Chủ -->
    @include('modals.promo-popup')

    <!-- Toast Notification -->
    @include('partials.toast')

</body>
</html>
