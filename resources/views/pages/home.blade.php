@extends('layouts.app')

@section('title', 'GAO - Gà Sốt & Cơm Hà Nội | Khám Phá Gà Giòn Sốt Đậm Vị')

@section('content')
    <!-- HERO DISCOVERY -->
    @if(($settings['section_hero_enabled'] ?? '1') == '1')
        @include('sections.hero')
    @endif

    <!-- SPOTLIGHT 4 VỊ SỐT (TEASER BANNER DẪN ĐẾN TRANG SỐT) -->
    @if(($settings['section_sauces_enabled'] ?? '1') == '1')
        @include('sections.home-sauces-banner')
    @endif

    <!-- TOP MÓN HOT BÁN CHẠY (MÓN ĐƯỢC GỌI NHIỀU) -->
    @if(($settings['section_popular_enabled'] ?? '1') == '1')
        @include('sections.popular-dishes')
    @endif

    <!-- COMBO TIẾT KIỆM -->
    @if(($settings['section_combos_enabled'] ?? '1') == '1')
        @include('sections.combos')
    @endif

    <!-- 3 CAM KẾT CHẤT LƯỢNG VÀNG -->
    @if(($settings['section_benefits_enabled'] ?? '1') == '1')
        @include('sections.benefits')
    @endif

    <!-- ĐÁNH GIÁ TỪ KHÁCH HÀNG HÀ NỘI -->
    @if(($settings['section_testimonials_enabled'] ?? '1') == '1')
        @include('sections.testimonials')
    @endif
@endsection
